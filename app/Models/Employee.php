<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = ['name', 'nip', 'position', 'birth_date', 'gender', 'address'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
