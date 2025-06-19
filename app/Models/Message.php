<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'question_text',
        'question_sent_at',
        'answer_text',
        'answer_sent_at',
        'status',
    ];

    protected $casts = [
        'question_sent_at' => 'datetime',
        'answer_sent_at'   => 'datetime',
    ];

    // Отключим массовое присвоение дат в question_sent_at/answer_sent_at
    protected $dates = [
        'question_sent_at',
        'answer_sent_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Пользователь‑отправитель
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Пользователь‑получатель
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
