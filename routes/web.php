<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AboutSchoolController;
use App\Http\Controllers\AdminLessonController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminPriceController;
use App\Http\Controllers\AdminReviewsController;
use App\Http\Controllers\AdminTimetableController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentCoursesController;
use App\Http\Controllers\StudentMessageController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentReviewsController;
use App\Http\Controllers\StudentStatisticsController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\TeacherCoursesController;
use App\Http\Controllers\TeacherMessageController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\TeacherStatisticsController;
use App\Http\Controllers\TeacherTimetableController;
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

// Авторизация
Auth::routes();

Route::get('/about-school', [AboutSchoolController::class, 'index'])->name('about.school');



// ПАНЕЛЬ АДМИНИСТРАТОРА
Route::get('/admin/statistics', [AdminStatisticsController::class, 'index'])
    ->name('admin.statistics');

// Заявки
// Отображение странички админа - "Заявки"
Route::get('/requests', [AdminRequestsController::class, 'index'])
    ->name('admin.requests');
// Обновление статуса заявки
Route::patch('/requests/{id}/update-role', [AdminRequestsController::class, 'updateRole'])
    ->name('admin.requests.updateRole');
// Создание профиля по заявке
Route::post('/requests/{id}/create-profile', [AdminRequestsController::class, 'createProfile'])
    ->name('admin.requests.createProfile');
// Массовое создание профилей по заявкам
Route::post('/requests/create-profiles-all', [AdminRequestsController::class, 'createProfilesAll'])
    ->name('admin.requests.createProfilesAll');


// Пользователи
Route::get('admin/users', [AdminUsersController::class, 'index'])
    ->name('admin.users.index');
Route::get('admin/users/create', [AdminUsersController::class, 'create'])
    ->name('admin.users.create');
Route::post('admin/users', [AdminUsersController::class, 'store'])
    ->name('admin.users.store');
Route::get('admin/users/{user}/edit', [AdminUsersController::class, 'edit'])
    ->name('admin.users.edit');
Route::put('admin/users/{user}', [AdminUsersController::class, 'update'])
    ->name('admin.users.update');
Route::delete('/admin/users/{user}', [AdminUsersController::class, 'destroy'])
    ->name('admin.users.destroy');


//Курсы
Route::get('admin/courses',         [AdminCoursesController::class, 'index'])->name('admin.courses.index');
Route::get('admin/courses/create',  [AdminCoursesController::class, 'create'])->name('admin.courses.create');
Route::post('admin/courses',        [AdminCoursesController::class, 'store'])->name('admin.courses.store');
Route::get('admin/courses/{course}/edit', [AdminCoursesController::class, 'edit'])->name('admin.courses.edit');
Route::put('admin/courses/{course}',      [AdminCoursesController::class, 'update'])->name('admin.courses.update');
Route::delete('admin/courses/{course}',   [AdminCoursesController::class, 'destroy'])->name('admin.courses.destroy');
// Страница участников
Route::get('courses/{course}/participants', [AdminCoursesController::class, 'participants'])
    ->name('admin.courses.participants');
// Добавить преподавателя
Route::post('admin/courses/{course}/teachers', [AdminCoursesController::class,'addTeacher'])
    ->name('admin.courses.addTeacher');
// Удалить преподавателя
Route::delete('admin/courses/{course}/teachers/{user}', [AdminCoursesController::class,'removeTeacher'])
    ->name('admin.courses.removeTeacher');
// Аналогично для студентов:
Route::post('admin/courses/{course}/students', [AdminCoursesController::class,'addStudent'])
    ->name('admin.courses.addStudent');
Route::delete('admin/courses/{course}/students/{user}', [AdminCoursesController::class,'removeStudent'])
    ->name('admin.courses.removeStudent');



// Тарифы
// Список тарифов
Route::get('admin/prices', [AdminPriceController::class, 'index'])
    ->name('admin.prices.index');
// Форма создания тарифа
Route::get('admin/prices/create', [AdminPriceController::class, 'create'])
    ->name('admin.prices.create');
// Сохранение нового тарифа
Route::post('admin/prices', [AdminPriceController::class, 'store'])
    ->name('admin.prices.store');
// Форма редактирования тарифа
Route::get('admin/prices/{price}/edit', [AdminPriceController::class, 'edit'])
    ->name('admin.prices.edit');
// Обновление тарифа
Route::put('admin/prices/{price}', [AdminPriceController::class, 'update'])
    ->name('admin.prices.update');
// (опционально) Удаление тарифа
Route::delete('admin/prices/{price}', [AdminPriceController::class, 'destroy'])
    ->name('admin.prices.destroy');


// Расписание
// Просмотр всех слотов
Route::get('admin/timetables', [AdminTimetableController::class, 'index'])
    ->name('admin.timetables.index');
// Форма создания нового слота
Route::get('admin/timetables/create', [AdminTimetableController::class, 'create'])
    ->name('admin.timetables.create');
// Сохранение нового слота
Route::post('admin/timetables', [AdminTimetableController::class, 'store'])
    ->name('admin.timetables.store');
// Форма редактирования слота
Route::get('admin/timetables/{timetable}/edit', [AdminTimetableController::class, 'edit'])
    ->name('admin.timetables.edit');
// Обновление слота
Route::put('admin/timetables/{timetable}', [AdminTimetableController::class, 'update'])
    ->name('admin.timetables.update');
// Удаление слота
Route::delete('admin/timetables/{timetable}', [AdminTimetableController::class, 'destroy'])
    ->name('admin.timetables.destroy');
// Форма редактирования отдельного слота на конкретную дату
Route::get(
    'admin/timetables/{timetable}/{date}/edit-slot',
    [AdminTimetableController::class, 'editSlot']
)->name('admin.timetables.editSlot');
// Обработка сохранения изменений (тоже с датой)
Route::patch(
    'admin/timetables/{timetable}/{date}/update-slot',
    [AdminTimetableController::class, 'updateSlot']
)->name('admin.timetables.updateSlot');




// Языки
// Показываем список языков
Route::get('admin/languages', [AdminLanguagesController::class, 'index'])
    ->name('admin.languages.index');
// Добавление нового языка
Route::post('admin/languages/create', [AdminLanguagesController::class, 'create'])
    ->name('admin.languages.create');
// Форма редактирования языка
Route::get('admin/languages/{language}/edit', [AdminLanguagesController::class, 'edit'])
    ->name('admin.languages.edit');
// Сохранение изменений после редактирования
Route::put('admin/languages/{language}', [AdminLanguagesController::class, 'update'])
    ->name('admin.languages.update');
// Удаление языка
Route::delete('admin/languages/{language}', [AdminLanguagesController::class, 'destroy'])
    ->name('admin.languages.destroy');


// Отзывы
// Список всех отзывов
Route::get('admin/reviews', [AdminReviewsController::class, 'index'])
    ->name('admin.reviews.index');
// Патч для изменения статуса
Route::patch('admin/reviews/{review}/status', [AdminReviewsController::class, 'updateStatus'])
    ->name('admin.reviews.updateStatus');
// Удаление
Route::delete('admin/reviews/{review}', [AdminReviewsController::class, 'destroy'])
    ->name('admin.reviews.destroy');


// Сообщения
// Админ: список сообщений
Route::get('admin/messages', [AdminMessageController::class, 'index'])
    ->name('admin.messages.index');
// Админ: форма создания нового сообщения
Route::get('admin/messages/create', [AdminMessageController::class, 'create'])
    ->name('admin.messages.create');
// Админ: сохранение нового сообщения
Route::post('admin/messages', [AdminMessageController::class, 'store'])
    ->name('admin.messages.store');
// Админ: просмотр конкретного сообщения и ответ
Route::get('admin/messages/{message}', [AdminMessageController::class, 'show'])
    ->name('admin.messages.show');
// Админ: отправка ответа на сообщение
Route::patch('admin/messages/{message}', [AdminMessageController::class, 'reply'])
    ->name('admin.messages.reply');







// ПАНЕЛЬ УЧИТЕЛЯ
Route::get('/teacher/statistics', [TeacherStatisticsController::class, 'index'])
    ->name('teacher.statistics');

// Курсы
// Страница "Мои курсы"
// Список курсов
Route::get('/teacher/courses', [TeacherCoursesController::class, 'courses'])
    ->name('teacher.courses.index');
// Форма редактирования уровня курса
Route::get('/teacher/courses/{course}/edit-level', [TeacherCoursesController::class, 'editCourseLevel'])
    ->name('teacher.courses.editLevel');
// Обновление уровня курса
Route::patch('/teacher/courses/{course}/update-level', [TeacherCoursesController::class, 'updateCourseLevel'])
    ->name('teacher.courses.updateLevel');

// Профиль
Route::get('teacher/profile', [TeacherProfileController::class, 'edit'])
    ->name('teacher.profile.edit');
Route::patch('teacher/profile', [TeacherProfileController::class, 'update'])
    ->name('teacher.profile.update');
Route::get('teacher/profile/password', [TeacherProfileController::class, 'showPasswordForm'])
    ->name('teacher.profile.password.show');
Route::patch('teacher/profile/password', [TeacherProfileController::class, 'updatePassword'])
    ->name('teacher.profile.password.update');

// Сообщения
// 1) Список сообщений
Route::get('teacher/messages', [TeacherMessageController::class, 'index'])
    ->name('teacher.messages.index');
// 2) Форма нового сообщения
Route::get('teacher/messages/create', [TeacherMessageController::class, 'create'])
    ->name('teacher.messages.create');
// 3) Сохранение нового сообщения
Route::post('teacher/messages', [TeacherMessageController::class, 'store'])
    ->name('teacher.messages.store');
// 4) Просмотр конкретного сообщения
Route::get('teacher/messages/{message}', [TeacherMessageController::class, 'show'])
    ->name('teacher.messages.show');
// 5) Отправка ответа на сообщение
Route::patch('teacher/messages/{message}', [TeacherMessageController::class, 'reply'])
    ->name('teacher.messages.reply');







// ПАНЕЛЬ СТУДЕНТА
Route::get('/student/statistics', [StudentStatisticsController::class, 'index'])
    ->name('student.statistics');

//Курсы
Route::get('/student/courses', [StudentCoursesController::class, 'index'])
    ->name('student.courses');


// Отзыв
// форма и список отзывов
Route::get('student/reviews', [StudentReviewsController::class, 'index'])
    ->name('student.reviews.index');
// именно этот POST‑маршрут нужен для store()
Route::post('student/reviews', [StudentReviewsController::class, 'store'])
    ->name('student.reviews.store');

// Профиль
Route::get('student/profile', [StudentProfileController::class, 'edit'])
    ->name('student.profile.edit');
Route::patch('student/profile', [StudentProfileController::class, 'update'])
    ->name('student.profile.update');
Route::get('student/profile/password', [StudentProfileController::class, 'showPasswordForm'])
    ->name('student.profile.password.show');
Route::patch('student/profile/password', [StudentProfileController::class, 'updatePassword'])
    ->name('student.profile.password.update');

// Сообщения
Route::get('student/messages',        [StudentMessageController::class,'index'])
    ->name('student.messages.index');
Route::get('student/messages/create', [StudentMessageController::class,'create'])
    ->name('student.messages.create');
Route::post('student/messages',       [StudentMessageController::class,'store'])
    ->name('student.messages.store');
Route::get('student.messages/{message}', [StudentMessageController::class,'show'])
    ->name('student.messages.show');
Route::patch('student/messages/{message}', [StudentMessageController::class,'reply'])
    ->name('student.messages.reply');








// Создание заявки в форме заявки
Route::post('/free-lesson-request', [FreeLessonRequestController::class, 'store'])
    ->name('free_lesson_request.store');





















Route::post('/feedback', [MainController::class, 'sendFeedback'])->name('feedback.store');


Route::post('/contact/send', [MainController::class, 'sendContact'])->name('contact.send');








