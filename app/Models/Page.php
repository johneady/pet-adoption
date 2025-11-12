<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string $status
 * @property bool $is_special
 * @property int|null $menu_id
 * @property int|null $submenu_id
 * @property bool $requires_auth
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Menu|null $menu
 * @property-read \App\Models\Menu|null $submenu
 *
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page visible()
 *
 * @mixin \Eloquent
 */
class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'is_special',
        'menu_id',
        'submenu_id',
        'requires_auth',
        'meta_title',
        'meta_description',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_special' => 'boolean',
            'requires_auth' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function submenu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'submenu_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeVisible(Builder $query): Builder
    {
        $query->where('status', 'published');

        if (! auth()->check()) {
            $query->where('requires_auth', false);
        }

        return $query;
    }

    protected static function booted(): void
    {
        static::creating(function (Page $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        static::deleting(function (Page $page) {
            if ($page->is_special) {
                throw new \Exception('Special pages cannot be deleted.');
            }
        });
    }
}
