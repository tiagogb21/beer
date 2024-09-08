<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'position',
        'description',
        'slug',
        'parent_id',
    ];

    protected $hidden = [];

    protected $casts = [
        'is_visible' => 'boolean',
        'position' => 'integer',
        'description' => 'string',
        'parent_id' => 'integer',
    ];

    /**
     * Define o relacionamento de uma categoria com sua categoria pai.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Define o relacionamento de uma categoria com suas subcategorias.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /** @return BelongsToMany<Product> */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_products', 'category_id', 'product_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('category-images');
    }
}
