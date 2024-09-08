<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'requires_shipping',
        'is_visible',
        'price',
        'quantity',
        'unit_id',
    ];

    protected $hidden = [];

    protected $casts = [
        'requires_shipping' => 'boolean',
        'is_visible' => 'boolean',
        'price' => 'decimal:2',
        'security_stock' => 'integer',
    ];

    /**
     * Define o relacionamento com a unidade.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('images');
    }

    public function measurements(): BelongsToMany
    {
        return $this->belongsToMany(Measurement::class, 'measurement_products', 'product_id', 'measurement_id');
    }
}
