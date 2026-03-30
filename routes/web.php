<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AdminController;

// --- PUBLIC ROUTES (Đăng nhập/Đăng ký) ---
Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);

// --- STUDENT ROUTES (Sinh viên làm bài) ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ExamController::class, 'dashboard']);
    Route::get('/exam/{id}', [ExamController::class, 'startExam']);
    Route::post('/exam/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
    Route::get('/history', [ExamController::class, 'history']);
    Route::get('/survey', [ExamController::class, 'survey']);
    Route::post('/survey', [ExamController::class, 'submitSurvey']);
    Route::post('/exam/log-cheat', [ExamController::class, 'logCheat'])->name('exam.log-cheat');
    
});

// --- ADMIN ROUTES (Khu vực Quản trị) ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // 1. Quản lý sinh viên
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store'); 
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    
    // 2. Quản lý đề thi
    Route::get('/quiz', [AdminController::class, 'quizzes'])->name('quizzes');
    Route::post('/quiz', [AdminController::class, 'storeQuiz'])->name('quiz.store');
    Route::put('/quiz/{id}', [AdminController::class, 'updateQuiz'])->name('quiz.update');
    Route::delete('/quiz/{id}', [AdminController::class, 'destroyQuiz'])->name('quiz.destroy');
    Route::post('/quiz/toggle-score/{id}', [AdminController::class, 'toggleScore'])->name('quiz.toggle-score');
    Route::get('/quiz/export/{id}', [AdminController::class, 'exportResults'])->name('quiz.export');
    
    // Quản lý câu hỏi trong đề thi
    Route::get('/quiz/{id}', [AdminController::class, 'questions'])->name('questions');
    Route::post('/quiz/{id}/questions', [AdminController::class, 'storeQuestion'])->name('questions.store');

   
    Route::get('/surveys', [AdminController::class, 'surveys'])->name('surveys');
});