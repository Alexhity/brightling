<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'language_id',
        'level',
        'title',
        'file_path',
        'issued_at',
    ];

    /**
     * Приведение типов.
     */
    protected $casts = [
        'issued_at' => 'datetime',
    ];

    /**
     * Связь: сертификат принадлежит пользователю.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь: сертификат по языку.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
