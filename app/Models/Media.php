<?php

namespace App\Models;

use App\Services\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'source',
        'disk',
        'path',
        'external_url',
        'alt_text_ar',
        'alt_text_en',
        'caption_ar',
        'caption_en',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot the model.
     * Clean up physical files from storage when a media record is deleted.
     */
    protected static function booted(): void
    {
        static::deleted(function (Media $media) {
            app(MediaService::class)->deletePhysicalFile($media);
        });
    }

    /**
     * Polymorphic parent relation.
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Resolve the public URL of the media item.
     * Supports both locally uploaded files (via Storage disk) and external CDN URLs.
     */
    public function getUrlAttribute(): string
    {
        if ($this->source === 'external' && !empty($this->external_url)) {
            return $this->external_url;
        }

        if (!empty($this->path)) {
            $disk = $this->disk ?: 'public';
            return Storage::disk($disk)->url($this->path);
        }

        return asset('images/placeholder-fabric.webp');
    }

    /**
     * Localized alternative text helper.
     */
    public function getAltTextAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->alt_text_ar ?: $this->alt_text_en) : ($this->alt_text_en ?: $this->alt_text_ar);
    }

    /**
     * Localized caption helper.
     */
    public function getCaptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->caption_ar ?: $this->caption_en) : ($this->caption_en ?: $this->caption_ar);
    }

    /**
     * Scope for primary media.
     */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope sorted by order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope for local images.
     */
    public function scopeLocal(Builder $query): Builder
    {
        return $query->where('source', 'local');
    }

    /**
     * Scope for external images.
     */
    public function scopeExternal(Builder $query): Builder
    {
        return $query->where('source', 'external');
    }
}
