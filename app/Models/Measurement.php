<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Measurement extends Model
{
    use HasFactory;

    protected $fillable = ['value', 'unit'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'measurement_products', 'measurement_id', 'product_id');
    }
}
