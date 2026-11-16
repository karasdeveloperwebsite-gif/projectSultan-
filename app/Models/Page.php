<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'title_ar',
        'title_en',
        'slug_ar',
        'slug_en',
        'content_ar',
        'content_en',
        'meta_title_ar',
        'meta_title_en',
        'meta_description_ar',
        'meta_description_en',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Localized title accessor.
     */
    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->title_ar : $this->title_en;
    }

    /**
     * Localized slug accessor.
     */
    public function getSlugAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->slug_ar : $this->slug_en;
    }

    /**
     * Localized content accessor.
     */
    public function getContentAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->content_ar ?: $this->content_en) : ($this->content_en ?: $this->content_ar);
    }

    /**
     * Localized SEO title accessor.
     */
    public function getMetaTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->meta_title_ar ?: $this->title_ar) : ($this->meta_title_en ?: $this->title_en);
    }

    /**
     * Localized SEO description accessor.
     */
    public function getMetaDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->meta_description_ar ?: $this->meta_description_en) : ($this->meta_description_en ?: $this->meta_description_ar);
    }

    /**
     * Scope for published pages.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope search by either Arabic or English slug.
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where(function ($q) use ($slug) {
            $q->where('slug_ar', $slug)->orWhere('slug_en', $slug);
        });
    }
}
