<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
