<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AiGeneratorController;

// ===================== PUBLIC ROUTES =====================
Route::get('/', function () {
    return redirect('/login');
})->name('home');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Quên mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}/{email}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');


// ===================== STUDENT ROUTES (ĐÃ SỬA LỖI BỌC BẢO VỆ) =====================
Route::middleware('student')->group(function () {
    Route::get('/dashboard', [ExamController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.password.update');

    // Bài thi
    Route::get('/exam/{id}', [ExamController::class, 'startExam'])->name('exam.start');
    Route::post('/exam/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
    Route::get('/history', [ExamController::class, 'history'])->name('exam.history');
    Route::post('/exam/log-cheat', [ExamController::class, 'logCheat'])->name('exam.log-cheat');

    // Khảo sát
    Route::get('/survey', [ExamController::class, 'survey'])->name('survey');
    Route::post('/survey', [ExamController::class, 'submitSurvey']);

    // Điểm danh QR
    Route::get('/attendance/scan/{token?}', [AttendanceController::class, 'showScanner'])->name('attendance.scan');
    Route::post('/attendance/submit', [AttendanceController::class, 'submitAttendance'])->name('attendance.submit');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
});


// ===================== ADMIN & TEACHER ROUTES =====================
Route::prefix('admin')
    ->name('admin.')
    ->middleware('admin')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        // Hồ sơ và Đổi mật khẩu Admin
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::post('/change-password', [AdminController::class, 'changePassword'])->name('password.update');
        // Quản lý sinh viên
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

        // Quản lý đề thi
        Route::get('/quizzes', [AdminController::class, 'quizzes'])->name('quizzes');
        Route::post('/quizzes', [AdminController::class, 'storeQuiz'])->name('quizzes.store');
        Route::put('/quizzes/{id}', [AdminController::class, 'updateQuiz'])->name('quizzes.update');
        Route::delete('/quizzes/{id}', [AdminController::class, 'deleteQuiz'])->name('quizzes.destroy');
        
        // Quản lý câu hỏi
        Route::get('/get-questions-by-subject/{subjectId}', [AdminController::class, 'getQuestionsBySubject'])->name('questions.by-subject');
        Route::get('/quiz/{id}', [AdminController::class, 'questions'])->name('quiz.questions');
        Route::post('/quiz/{id}/questions', [AdminController::class, 'storeQuestion'])->name('questions.store');
        Route::delete('/questions/{id}', [AdminController::class, 'deleteQuestion'])->name('questions.delete');

        // Điểm danh
        Route::get('/attendance', [AdminController::class, 'attendance'])->name('attendance');
        Route::post('/attendance/generate', [AttendanceController::class, 'generateQr'])->name('attendance.generate');
        Route::get('/attendance/{id}', [AdminController::class, 'attendanceDetail'])->name('attendance.detail');

        // Khảo sát
        Route::get('/surveys', [AdminController::class, 'surveys'])->name('surveys');

        // AI Generator
        Route::get('/ai-generate', [AdminController::class, 'aiGenerateForm'])->name('ai-generate');
        Route::post('/ai-generate', [AdminController::class, 'aiGenerateQuestions'])->name('ai-generate.store');
        Route::post('/ai/generate-questions', [AiGeneratorController::class, 'generateQuestions'])->name('ai.generate-questions');

        // Thống kê kết quả
        Route::get('/results', [AdminController::class, 'results'])->name('results');
        Route::get('/quiz/{id}/results', [AdminController::class, 'quizResults'])->name('quiz.results');
        Route::get('/user/{id}/results', [AdminController::class, 'userResults'])->name('user.results');
        Route::get('/quiz/{quizId}/export', [AdminController::class, 'exportResults'])->name('quiz.export');
    });


// ===================== CHUNG (ADMIN + STUDENT) =====================
// Đưa route xem chi tiết ra đây để Admin bấm vào không bị văng ra Login
Route::get('/exam/{attemptId}/detail', [ExamController::class, 'examDetail'])->name('exam.detail');