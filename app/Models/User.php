<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'date_birthday',
        'description',
        'role',
        'file_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'language_user')
            ->withPivot('level')    // теперь в pivot есть level
            ->withTimestamps();
    }

//    public function courses()
//    {
//        return $this->belongsToMany(
//            Course::class,
//            'user_courses',
//            'user_id',
//            'course_id'
//        )->withTimestamps();
//    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Курсы, где пользователь — преподаватель.
     */
    public function taughtCourses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('role')
            ->wherePivot('role', 'teacher')
            ->withTimestamps();
    }

    /**
     * Курсы, где пользователь — студент.
     */
    public function studentCourses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('role')
            ->wherePivot('role', 'student')
            ->withTimestamps();
    }

    public function lessons()
    {
        return $this->belongsToMany(
            Lesson::class,
            'lesson_user',   // точно то же имя таблицы
            'user_id',       // FK для User в pivot
            'lesson_id'      // FK для Lesson в pivot
        )->withPivot('role')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function timetables()
    {
        return $this->belongsToMany(
            Timetable::class,
            'timetable_user',
            'user_id',
            'timetable_id'
        )->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Сообщения, отправленные этим пользователем.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Сообщения, полученные этим пользователем.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function lesson_users()
    {
        return $this->hasMany(UserLesson::class);
    }




}


