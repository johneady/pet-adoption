<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /** @use HasFactory<\Database\Factories\SettingFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'string',
        ];
    }

    /**
     * Get the typed value of the setting.
     */
    public function getTypedValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'array', 'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Set the value with proper type casting.
     */
    public function setTypedValue(mixed $value): void
    {
        $this->value = match ($this->type) {
            'boolean' => $value ? '1' : '0',
            'array', 'json' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", now()->addDay(), function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->getTypedValue() : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $group = 'general'): self
    {
        $setting = static::firstOrNew(['key' => $key]);

        if ($type) {
            $setting->type = $type;
        }

        if ($group) {
            $setting->group = $group;
        }

        $setting->setTypedValue($value);
        $setting->save();

        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Get all settings grouped by group.
     */
    public static function getAllGrouped(): array
    {
        return static::all()->groupBy('group')->map(function ($settings) {
            return $settings->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getTypedValue()];
            });
        })->toArray();
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }

    protected static function booted(): void
    {
        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }
}
