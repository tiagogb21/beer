<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'addressable',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
    ];

    /** @return BelongsToMany<User> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'address_users', 'address_id', 'user_id');
    }
}
