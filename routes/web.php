<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AboutSchoolController;
use App\Http\Controllers\AdminCertificateController;
use App\Http\Controllers\AdminLessonController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminPriceController;
use App\Http\Controllers\AdminReviewsController;
use App\Http\Controllers\AdminTimetableController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentCoursesController;
use App\Http\Controllers\StudentHomeworkController;
use App\Http\Controllers\StudentLessonController;
use App\Http\Controllers\StudentMessageController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentReviewsController;
use App\Http\Controllers\StudentStatisticsController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\TeacherCoursesController;
use App\Http\Controllers\TeacherHomeworkController;
use App\Http\Controllers\TeacherLessonController;
use App\Http\Controllers\TeacherMessageController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\TeacherStatisticsController;
use App\Http\Controllers\TeacherTimetableController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsStudent;
use App\Http\Middleware\IsTeacher;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminStatisticsController;
use App\Http\Controllers\AdminRequestsController;
use App\Http\Controllers\AdminCoursesController;
use App\Http\Controllers\AdminLanguagesController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FreeLessonRequestController;
use App\Http\Controllers\MainController;

// ГЛАВНАЯ
Route::get('/', [MainController::class, 'index'])->name('main');
Route::get('/newAdminCreateeyeyeye', [MainController::class, 'newAdmin'])->name('newAdminCreateeyeyeye');


// Простая запись на курс
Route::post('/enroll/{course}', function ($courseId) {
    $user = auth()->user();

    // Проверка что пользователь студент
    if ($user->role !== 'student') {
        return back()->withErrors('Только студенты могут записываться на курсы');
    }

    // Мгновенная запись без подтверждения
    $user->courses()->syncWithoutDetaching([$courseId => [
        'enrolled_at' => now(),
        'status' => 'active'
    ]]);

    return back()->with('success', 'Вы успешно записаны на курс!');
})->name('enroll.simple')->middleware('auth');

// Авторизация
Auth::routes();

Route::get('/courses', [\App\Http\Controllers\CourseController::class, 'index'])
    ->name('courses');



Route::get('/about-school', [AboutSchoolController::class, 'index'])->name('about.school');

Route::get('/teachers', [App\Http\Controllers\TeacherController::class, 'index'])
    ->name('teachers');

Route::get('/prices', [App\Http\Controllers\PriceController::class, 'index'])
    ->name('prices');

// Создание заявки в форме заявки
Route::post('/test-lesson-request', [FreeLessonRequestController::class, 'store'])
    ->name('free_lesson_request.store');


// В routes/web.php
Route::get('test-lesson', [FreeLessonRequestController::class, 'showForm'])->name('free_lesson.form');


Route::post('/feedback', [MainController::class, 'sendFeedback'])->name('feedback.store');


Route::post('/contact/send', [MainController::class, 'sendContact'])->name('contact.send');





// ПАНЕЛЬ АДМИНИСТРАТОРА

Route::get('/admin/statistics', [AdminStatisticsController::class, 'index'])
    ->name('admin.statistics')->middleware([ IsAdmin::class]);

// Заявки
// Отображение странички админа - "Заявки"
Route::get('requests', [AdminRequestsController::class, 'index'])
    ->name('admin.requests')->middleware([ IsAdmin::class]);

// Обновление статуса заявки
Route::patch('/requests/{id}/update-role', [AdminRequestsController::class, 'updateRole'])
    ->name('admin.requests.updateRole')->middleware([ IsAdmin::class]);
// Создание профиля по заявке
Route::post('/requests/{id}/create-profile', [AdminRequestsController::class, 'createProfile'])
    ->name('admin.requests.createProfile')->middleware([ IsAdmin::class]);
// Массовое создание профилей по заявкам
Route::post('/requests/create-profiles-all', [AdminRequestsController::class, 'createProfilesAll'])
    ->name('admin.requests.createProfilesAll')->middleware([ IsAdmin::class]);
Route::delete('admin/requests/{id}', [FreeLessonRequestController::class, 'destroy'])
    ->name('admin.requests.destroy')->middleware([ IsAdmin::class]);


// Пользователи
Route::get('admin/users', [AdminUsersController::class, 'index'])
    ->name('admin.users.index')->middleware([ IsAdmin::class]);
Route::get('admin/users/create', [AdminUsersController::class, 'create'])
    ->name('admin.users.create')->middleware([ IsAdmin::class]);
Route::post('admin/users', [AdminUsersController::class, 'store'])
    ->name('admin.users.store')->middleware([ IsAdmin::class]);
Route::get('admin/users/{user}/edit', [AdminUsersController::class, 'edit'])
    ->name('admin.users.edit')->middleware([ IsAdmin::class]);
Route::put('admin/users/{user}', [AdminUsersController::class, 'update'])
    ->name('admin.users.update')->middleware([ IsAdmin::class]);
Route::delete('/admin/users/{user}', [AdminUsersController::class, 'destroy'])
    ->name('admin.users.destroy')->middleware([ IsAdmin::class]);


//Курсы
Route::get('admin/courses',         [AdminCoursesController::class, 'index'])
    ->name('admin.courses.index')->middleware([ IsAdmin::class]);
Route::get('admin/courses/create',  [AdminCoursesController::class, 'create'])
    ->name('admin.courses.create')->middleware([ IsAdmin::class]);
Route::post('admin/courses',        [AdminCoursesController::class, 'store'])
    ->name('admin.courses.store')->middleware([ IsAdmin::class]);
Route::get('admin/courses/{course}/edit', [AdminCoursesController::class, 'edit'])
    ->name('admin.courses.edit')->middleware([ IsAdmin::class]);
Route::put('admin/courses/{course}',      [AdminCoursesController::class, 'update'])
    ->name('admin.courses.update')->middleware([ IsAdmin::class]);
Route::delete('admin/courses/{course}',   [AdminCoursesController::class, 'destroy'])
    ->name('admin.courses.destroy')->middleware([ IsAdmin::class]);
// Страница участников
Route::get('courses/{course}/participants', [AdminCoursesController::class, 'participants'])
    ->name('admin.courses.participants')->middleware([ IsAdmin::class]);
// Добавить преподавателя
Route::post('admin/courses/{course}/teachers', [AdminCoursesController::class,'addTeacher'])
    ->name('admin.courses.addTeacher')->middleware([ IsAdmin::class]);
// Удалить преподавателя
Route::delete('admin/courses/{course}/teachers/{user}', [AdminCoursesController::class,'removeTeacher'])
    ->name('admin.courses.removeTeacher')->middleware([ IsAdmin::class]);
// Аналогично для студентов:
Route::post('admin/courses/{course}/students', [AdminCoursesController::class,'addStudent'])
    ->name('admin.courses.addStudent')->middleware([ IsAdmin::class]);
Route::delete('admin/courses/{course}/students/{user}', [AdminCoursesController::class,'removeStudent'])
    ->name('admin.courses.removeStudent')->middleware([ IsAdmin::class]);


// Тарифы
// Список тарифов
Route::get('admin/prices', [AdminPriceController::class, 'index'])
    ->name('admin.prices.index')->middleware([ IsAdmin::class]);
// Форма создания тарифа
Route::get('admin/prices/create', [AdminPriceController::class, 'create'])
    ->name('admin.prices.create')->middleware([ IsAdmin::class]);
// Сохранение нового тарифа
Route::post('admin/prices', [AdminPriceController::class, 'store'])
    ->name('admin.prices.store')->middleware([ IsAdmin::class]);
// Форма редактирования тарифа
Route::get('admin/prices/{price}/edit', [AdminPriceController::class, 'edit'])
    ->name('admin.prices.edit')->middleware([ IsAdmin::class]);
// Обновление тарифа
Route::put('admin/prices/{price}', [AdminPriceController::class, 'update'])
    ->name('admin.prices.update')->middleware([ IsAdmin::class]);
// (опционально) Удаление тарифа
Route::delete('admin/prices/{price}', [AdminPriceController::class, 'destroy'])
    ->name('admin.prices.destroy')->middleware([ IsAdmin::class]);


// Расписание
// Timetable Routes
Route::prefix('admin/timetables')->group(function () {
    Route::get('/', [AdminTimetableController::class, 'index'])->name('admin.timetables.index')->middleware([ IsAdmin::class])  ;

//    Route::delete('/{timetable}', [AdminTimetableController::class, 'destroy'])->name('admin.timetables.destroy')->middleware([ IsAdmin::class]) ;
    // Удаление исключения
    Route::delete('/timetables/exceptions/{timetable}',
        [AdminTimetableController::class, 'deleteException'])
        ->name('admin.timetables.deleteException')->middleware([ IsAdmin::class]) ;

    // Показать форму редактирования
    Route::get('admin/timetables/{timetable}/edit-slot/{date}',
        [AdminTimetableController::class, 'editSlot'])
        ->name('admin.timetables.edit-slot')->middleware([ IsAdmin::class]) ;

// Обработать изменения
    Route::put('admin/timetables/{timetable}/update-slot/{date}',
        [AdminTimetableController::class, 'updateSlot'])
        ->name('admin.timetables.update-slot')->middleware([ IsAdmin::class]) ;

    Route::get('/admin/timetables/create-slot', [AdminTimetableController::class, 'createSlot'])
        ->name('admin.timetables.create-slot')
        ->middleware([ IsAdmin::class]);

    Route::post('/admin/timetables/store-slot', [AdminTimetableController::class, 'storeSlot'])
        ->name('admin.timetables.store-slot')
        ->middleware([ IsAdmin::class]);

    Route::delete('/admin/timetables/{timetable}/destroy-slot', [AdminTimetableController::class, 'destroySlot'])
        ->name('admin.timetables.destroy-slot')
        ->middleware([ IsAdmin::class]);
});





// Языки
// Показываем список языков
Route::get('admin/languages', [AdminLanguagesController::class, 'index'])
    ->name('admin.languages.index')->middleware([ IsAdmin::class]) ;
// Добавление нового языка
Route::post('admin/languages/create', [AdminLanguagesController::class, 'create'])
    ->name('admin.languages.create')->middleware([ IsAdmin::class]) ;
// Форма редактирования языка
Route::get('admin/languages/{language}/edit', [AdminLanguagesController::class, 'edit'])
    ->name('admin.languages.edit')->middleware([ IsAdmin::class]) ;
// Сохранение изменений после редактирования
Route::put('admin/languages/{language}', [AdminLanguagesController::class, 'update'])
    ->name('admin.languages.update')->middleware([ IsAdmin::class]) ;
// Удаление языка
Route::delete('admin/languages/{language}', [AdminLanguagesController::class, 'destroy'])
    ->name('admin.languages.destroy')->middleware([ IsAdmin::class]) ;


// Отзывы
// Список всех отзывов
Route::get('admin/reviews', [AdminReviewsController::class, 'index'])
    ->name('admin.reviews.index')->middleware([ IsAdmin::class]) ;
// Патч для изменения статуса
Route::patch('admin/reviews/{review}/status', [AdminReviewsController::class, 'updateStatus'])
    ->name('admin.reviews.updateStatus')->middleware([ IsAdmin::class]) ;
// Удаление
Route::delete('admin/reviews/{review}', [AdminReviewsController::class, 'destroy'])
    ->name('admin.reviews.destroy')->middleware([ IsAdmin::class]) ;


// Сообщения
// Админ: список сообщений
Route::get('admin/messages', [AdminMessageController::class, 'index'])
    ->name('admin.messages.index')->middleware([ IsAdmin::class]);
// Админ: форма создания нового сообщения
Route::get('admin/messages/create', [AdminMessageController::class, 'create'])
    ->name('admin.messages.create')->middleware([ IsAdmin::class]);
// Админ: сохранение нового сообщения
Route::post('admin/messages', [AdminMessageController::class, 'store'])
    ->name('admin.messages.store')->middleware([ IsAdmin::class]);
// Админ: просмотр конкретного сообщения и ответ
Route::get('admin/messages/{message}', [AdminMessageController::class, 'show'])
    ->name('admin.messages.show')->middleware([ IsAdmin::class]);
// Админ: отправка ответа на сообщение
Route::patch('admin/messages/{message}', [AdminMessageController::class, 'reply'])
    ->name('admin.messages.reply')->middleware([ IsAdmin::class]);

// Сертификаты
Route::get('admin/certificates', [AdminCertificateController::class, 'index'])
    ->name('admin.certificates.index')->middleware([ IsAdmin::class]);
Route::get('admin/certificates/create', [AdminCertificateController::class, 'create'])
    ->name('admin.certificates.create')->middleware([ IsAdmin::class]);
Route::post('admin/certificates', [AdminCertificateController::class, 'store'])
    ->name('admin.certificates.store')->middleware([ IsAdmin::class]);
Route::get('admin/certificates/{id}/edit', [AdminCertificateController::class, 'edit'])
    ->name('admin.certificates.edit')->middleware([ IsAdmin::class]);
Route::put('admin/certificates/{id}', [AdminCertificateController::class, 'update'])
    ->name('admin.certificates.update')->middleware([ IsAdmin::class]);
Route::delete('admin/certificates/{id}', [AdminCertificateController::class, 'destroy'])
    ->name('admin.certificates.destroy')->middleware([ IsAdmin::class]);






// ПАНЕЛЬ УЧИТЕЛЯ

Route::get('/teacher/statistics', [TeacherStatisticsController::class, 'index'])
    ->name('teacher.statistics')->middleware([ IsTeacher::class]);

Route::get('/teacher/timetable', [TeacherTimetableController::class, 'index'])
    ->name('teacher.timetable')->middleware([ IsTeacher::class]);


// Уроки
Route::get('lessons', [\App\Http\Controllers\TeacherLessonController::class,'index'])
    ->name('teacher.lessons.index')->middleware([ IsTeacher::class]);
Route::get('lessons/{lesson}/edit', [\App\Http\Controllers\TeacherLessonController::class,'edit'])
    ->name('teacher.lessons.edit')->middleware([ IsTeacher::class]);
Route::put('lessons/{lesson}', [\App\Http\Controllers\TeacherLessonController::class,'update'])
    ->name('teacher.lessons.update')->middleware([ IsTeacher::class]);


// Курсы
// Страница "Мои курсы"
// Список курсов
Route::get('/teacher/courses', [TeacherCoursesController::class, 'courses'])
    ->name('teacher.courses.index')->middleware([ IsTeacher::class]);
// Форма редактирования уровня курса
Route::get('/teacher/courses/{course}/edit-level', [TeacherCoursesController::class, 'editCourseLevel'])
    ->name('teacher.courses.editLevel')->middleware([ IsTeacher::class]);
// Обновление уровня курса
Route::patch('/teacher/courses/{course}/update-level', [TeacherCoursesController::class, 'updateCourseLevel'])
    ->name('teacher.courses.updateLevel')->middleware([ IsTeacher::class]);

// Профиль
Route::get('teacher/profile', [TeacherProfileController::class, 'edit'])
    ->name('teacher.profile.edit')->middleware([ IsTeacher::class]);
Route::patch('teacher/profile', [TeacherProfileController::class, 'update'])
    ->name('teacher.profile.update')->middleware([ IsTeacher::class]);
Route::get('teacher/profile/password', [TeacherProfileController::class, 'showPasswordForm'])
    ->name('teacher.profile.password.show')->middleware([ IsTeacher::class]);
Route::patch('teacher/profile/password', [TeacherProfileController::class, 'updatePassword'])
    ->name('teacher.profile.password.update')->middleware([ IsTeacher::class]);

// Сообщения
// 1) Список сообщений
Route::get('teacher/messages', [TeacherMessageController::class, 'index'])
    ->name('teacher.messages.index')->middleware([ IsTeacher::class]);
// 2) Форма нового сообщения
Route::get('teacher/messages/create', [TeacherMessageController::class, 'create'])
    ->name('teacher.messages.create')->middleware([ IsTeacher::class]);
// 3) Сохранение нового сообщения
Route::post('teacher/messages', [TeacherMessageController::class, 'store'])
    ->name('teacher.messages.store')->middleware([ IsTeacher::class]);
// 4) Просмотр конкретного сообщения
Route::get('teacher/messages/{message}', [TeacherMessageController::class, 'show'])
    ->name('teacher.messages.show')->middleware([ IsTeacher::class]);
// 5) Отправка ответа на сообщение
Route::patch('teacher/messages/{message}', [TeacherMessageController::class, 'reply'])
    ->name('teacher.messages.reply')->middleware([ IsTeacher::class]);

Route::get('teacher/homeworks', [App\Http\Controllers\TeacherHomeworkController::class,'index'])
    ->name('teacher.homeworks.index')->middleware([ IsTeacher::class]);
Route::get('teacher/homeworks/create', [App\Http\Controllers\TeacherHomeworkController::class,'create'])
    ->name('teacher.homeworks.create')->middleware([ IsTeacher::class]);
Route::post('teacher/homeworks', [App\Http\Controllers\TeacherHomeworkController::class,'store'])
    ->name('teacher.homeworks.store')->middleware([ IsTeacher::class]);
Route::get('teacher/homeworks/{homework}/edit', [App\Http\Controllers\TeacherHomeworkController::class,'edit'])
    ->name('teacher.homeworks.edit')->middleware([ IsTeacher::class]);
Route::put('teacher/homeworks/{homework}', [TeacherHomeworkController::class,'update'])
    ->name('teacher.homeworks.update')->middleware([ IsTeacher::class]);
Route::delete('homeworks/{homework}',   [TeacherHomeworkController::class,'destroy'])
    ->name('teacher.homeworks.destroy')->middleware([ IsTeacher::class]);




// ПАНЕЛЬ СТУДЕНТА
Route::get('/student/statistics', [StudentStatisticsController::class, 'index'])
    ->name('student.statistics')->middleware([ IsStudent::class]);

Route::get('/student/timetable', [StudentTimetableController::class, 'index'])
    ->name('student.timetable')->middleware([ IsStudent::class]);

//Курсы
Route::get('/student/courses', [StudentCoursesController::class, 'index'])
    ->name('student.courses')->middleware([ IsStudent::class]);


// Отзыв
// форма и список отзывов
Route::get('student/reviews', [StudentReviewsController::class, 'index'])
    ->name('student.reviews.index')->middleware([ IsStudent::class]);
// именно этот POST‑маршрут нужен для store()
Route::post('student/reviews', [StudentReviewsController::class, 'store'])
    ->name('student.reviews.store')->middleware([ IsStudent::class]);

// Профиль
Route::get('student/profile', [StudentProfileController::class, 'edit'])
    ->name('student.profile.edit')->middleware([ IsStudent::class]);
Route::patch('student/profile', [StudentProfileController::class, 'update'])
    ->name('student.profile.update')->middleware([ IsStudent::class]);
Route::get('student/profile/password', [StudentProfileController::class, 'showPasswordForm'])
    ->name('student.profile.password.show')->middleware([ IsStudent::class]);
Route::patch('student/profile/password', [StudentProfileController::class, 'updatePassword'])
    ->name('student.profile.password.update')->middleware([ IsStudent::class]);

// Сообщения
Route::get('student/messages',        [StudentMessageController::class,'index'])
    ->name('student.messages.index')->middleware([ IsStudent::class]);
Route::get('student/messages/create', [StudentMessageController::class,'create'])
    ->name('student.messages.create')->middleware([ IsStudent::class]);
Route::post('student/messages',       [StudentMessageController::class,'store'])
    ->name('student.messages.store')->middleware([ IsStudent::class]);
Route::get('student.messages/{message}', [StudentMessageController::class,'show'])
    ->name('student.messages.show')->middleware([ IsStudent::class]);
Route::patch('student/messages/{message}', [StudentMessageController::class,'reply'])
    ->name('student.messages.reply')->middleware([ IsStudent::class]);

//
Route::get('student/homeworks', [StudentHomeworkController::class, 'studentIndex'])
    ->name('student.homeworks')->middleware([ IsStudent::class]);

// уроки

Route::get('student/lessons', [StudentLessonController::class, 'index'])
    ->name('student.lessons.index')->middleware([ IsStudent::class]);

Route::get('student/attendance', [AttendanceController::class, 'show'])
    ->name('student.attendance')->middleware([ IsStudent::class]);


Route::post('/courses/enroll', [CourseController::class, 'enroll'])
    ->name('courses.enroll');






























