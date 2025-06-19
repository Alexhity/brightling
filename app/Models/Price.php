<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'name',
        'lesson_duration',
        'unit_price',
        'format',
    ];

    public function courses()
    {
        return $this->hasMany(
            Course::class,
            'price_id',
            'id'
        );
    }

}
