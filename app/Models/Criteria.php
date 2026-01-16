<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    protected $table = 'criteria';

    protected $fillable = ['code', 'name', 'type', 'weight'];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
