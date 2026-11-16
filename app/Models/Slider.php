<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'title_ar',
        'title_en',
        'subtitle_ar',
        'subtitle_en',
        'button_text_ar',
        'button_text_en',
        'button_url',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * Localized headline accessor.
     */
    public function getTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->title_ar ?: $this->title_en) : ($this->title_en ?: $this->title_ar);
    }

    /**
     * Localized subtitle accessor.
     */
    public function getSubtitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->subtitle_ar ?: $this->subtitle_en) : ($this->subtitle_en ?: $this->subtitle_ar);
    }

    /**
     * Localized button text accessor.
     */
    public function getButtonTextAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->button_text_ar ?: $this->button_text_en) : ($this->button_text_en ?: $this->button_text_ar);
    }

    /**
     * Scope for active sliders.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for currently running scheduled sliders.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    /**
     * Scope sorted by order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
