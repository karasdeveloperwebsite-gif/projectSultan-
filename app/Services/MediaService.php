<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    /**
     * Cache of already deleted disk:path entries in the current request/lifecycle to prevent duplicate operations.
     *
     * @var array<string, bool>
     */
    protected static array $deletedPaths = [];

    /**
     * Attach a local uploaded image to a model as primary or secondary media.
     */
    public function attachLocal(
        Model $model,
        UploadedFile $file,
        bool $isPrimary = false,
        ?string $altAr = null,
        ?string $altEn = null,
        string $disk = 'public',
        string $directory = 'media'
    ): Media {
        if ($isPrimary) {
            $this->resetPrimary($model);
        }

        $extension = $file->getClientOriginalExtension() ?: 'webp';
        $filename = Str::random(40) . '.' . $extension;
        $path = $file->storeAs($directory, $filename, $disk);

        return $model->media()->create([
            'source' => 'local',
            'disk' => $disk,
            'path' => $path,
            'alt_text_ar' => $altAr,
            'alt_text_en' => $altEn,
            'is_primary' => $isPrimary,
            'sort_order' => $model->media()->count() + 1,
        ]);
    }

    /**
     * Attach an external CDN URL to a model.
     */
    public function attachExternal(
        Model $model,
        string $url,
        bool $isPrimary = false,
        ?string $altAr = null,
        ?string $altEn = null
    ): Media {
        if ($isPrimary) {
            $this->resetPrimary($model);
        }

        return $model->media()->create([
            'source' => 'external',
            'external_url' => trim($url),
            'alt_text_ar' => $altAr,
            'alt_text_en' => $altEn,
            'is_primary' => $isPrimary,
            'sort_order' => $model->media()->count() + 1,
        ]);
    }

    /**
     * Reset is_primary flag for existing media on the parent model.
     */
    public function resetPrimary(Model $model): void
    {
        $model->media()->where('is_primary', true)->update(['is_primary' => false]);
    }

    /**
     * Delete a media item and its physical file if stored locally.
     * The physical file deletion is triggered via the Media model deleted event.
     */
    public function deleteMedia(Media $media): bool
    {
        return (bool) $media->delete();
    }

    /**
     * Delete all media items and associated physical files for a given model.
     */
    public function clearMediaFor(Model $model): void
    {
        if (method_exists($model, 'clearMedia')) {
            $model->clearMedia();
        } elseif (method_exists($model, 'media')) {
            $model->media()->cursor()->each(function (Media $media) {
                $media->delete();
            });
        }
    }

    /**
     * Safely delete physical file from storage for a given Media record.
     * Respects transactions via DB::afterCommit to prevent file loss on rollbacks.
     */
    public function deletePhysicalFile(Media $media, bool $immediate = false): bool
    {
        // 1. Guard against external CDN / non-local media
        if ($media->source !== 'local') {
            return false;
        }

        $rawPath = $media->path;
        if (empty($rawPath)) {
            return false;
        }

        // 2. Guard against external URL strings stored in path or scheme prefixes
        if (preg_match('/^(https?:)?\/\//i', $rawPath)) {
            return false;
        }

        // 3. Security: Prevent path traversal
        if (str_contains($rawPath, '..')) {
            Log::warning("Suspicious media path traversal prevented: {$rawPath}", [
                'media_id' => $media->id,
            ]);
            return false;
        }

        $path = ltrim($rawPath, '/\\');
        $disk = $media->disk ?: 'public';
        $mediaId = $media->id;

        $deletionTask = function () use ($disk, $path, $mediaId) {
            $this->performPhysicalDeletion($disk, $path, $mediaId);
        };

        if ($immediate || DB::transactionLevel() === 0) {
            $deletionTask();
        } else {
            DB::afterCommit($deletionTask);
        }

        return true;
    }

    /**
     * Perform the actual storage deletion safely.
     */
    public function performPhysicalDeletion(string $disk, string $path, ?int $mediaId = null): bool
    {
        $cacheKey = "{$disk}:{$path}";

        // Prevent duplicate deletion operations in the same lifecycle
        if (isset(self::$deletedPaths[$cacheKey])) {
            return true;
        }

        try {
            // Guard: ensure no other Media record references the exact same disk and path
            $query = Media::where('disk', $disk)->where('path', $path);
            if ($mediaId !== null) {
                $query->where('id', '!=', $mediaId);
            }

            if ($query->exists()) {
                Log::info("Skipped deleting physical file '{$path}' on disk '{$disk}' because it is still referenced by another Media record.", [
                    'media_id' => $mediaId,
                ]);
                return false;
            }

            $storage = Storage::disk($disk);
            if ($storage->exists($path)) {
                $storage->delete($path);
                self::$deletedPaths[$cacheKey] = true;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to delete media file '{$path}' on disk '{$disk}' (Media ID: {$mediaId}): " . $e->getMessage(), [
                'exception' => $e,
                'media_id' => $mediaId,
                'disk' => $disk,
                'path' => $path,
            ]);
            return false;
        }
    }

    /**
     * Reset the deleted paths tracking cache (useful for testing).
     */
    public static function resetDeletedPaths(): void
    {
        self::$deletedPaths = [];
    }
}
