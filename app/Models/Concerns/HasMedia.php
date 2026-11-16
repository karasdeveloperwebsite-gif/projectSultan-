<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMedia
{
    /**
     * Boot the trait to clean up media on parent deletion.
     */
    protected static function bootHasMedia(): void
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->clearMedia();
        });
    }

    /**
     * Safely delete all associated media records triggering individual model events.
     */
    public function clearMedia(): void
    {
        $this->media()->cursor()->each(function (Media $media) {
            $media->delete();
        });
    }

    /**
     * All media records associated with the model.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The primary image for this model.
     */
    public function primaryImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')->where('is_primary', true);
    }

    /**
     * Convenient accessor for primary image URL with fallback.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->primaryImage?->url ?? $this->media->first()?->url ?? asset('images/placeholder-fabric.webp');
    }
}
