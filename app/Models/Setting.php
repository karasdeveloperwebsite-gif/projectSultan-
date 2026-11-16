<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected const CACHE_KEY = 'site_settings_all_dict';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    /**
     * Boot model to invalidate cache upon any modification.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Retrieve a setting value by key with optional fallback and locale resolution.
     */
    public static function get(string $key, mixed $default = null, ?string $locale = null): mixed
    {
        $settings = Cache::rememberForever(self::CACHE_KEY, function () {
            return static::pluck('value', 'key')->all();
        });

        if (!array_key_exists($key, $settings)) {
            return $default;
        }

        $val = $settings[$key];

        // Decode JSON for complex or bilingual settings
        $decoded = json_decode((string) $val, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $targetLocale = $locale ?: app()->getLocale();
            if (isset($decoded[$targetLocale])) {
                return $decoded[$targetLocale];
            }
            if (isset($decoded['ar']) || isset($decoded['en'])) {
                return $decoded['ar'] ?? $decoded['en'] ?? $decoded;
            }
            return $decoded;
        }

        return $val ?? $default;
    }

    /**
     * Store or update a setting.
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?string $type = null): self
    {
        $storedValue = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'group' => $group,
                'type' => $type ?: (is_array($value) ? 'json' : 'string'),
            ]
        );
    }
}
