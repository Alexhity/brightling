<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['user_id', 'title', 'file_path'];

    /**
     * Связь: сертификат принадлежит пользователю.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}


