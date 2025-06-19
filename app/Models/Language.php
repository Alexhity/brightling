<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function freeLessonRequests()
    {
        return $this->hasMany(
            FreeLessonRequest::class,
            'language_id',
            'id'
        );
    }

    public function courses()
    {
        return $this->hasMany(
            Course::class,
            'language_id',
            'id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'language_user')
            ->withPivot('level')
            ->withTimestamps();
    }

//    Отношения


}
