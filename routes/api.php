<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

// Authentication
Route::post('/login', [ApiController::class, 'login'])->withoutMiddleware('api.key');
Route::post('/register', [ApiController::class, 'register'])->withoutMiddleware('api.key');

// Protected API routes (require API key)
Route::middleware('api.key')->group(function () {
    // Auth
    Route::get('/user', [ApiController::class, 'getProfile']);
    Route::put('/user/profile', [ApiController::class, 'updateProfile']);
    Route::post('/api-key/generate', [ApiController::class, 'generateApiKey']);

    // Quizzes
    Route::get('/quizzes', [ApiController::class, 'listQuizzes']);
    Route::get('/quizzes/{id}', [ApiController::class, 'getQuiz']);

    // Exam Attempts
    Route::post('/exam/start', [ApiController::class, 'startExam']);
    Route::post('/exam/submit-answer', [ApiController::class, 'submitAnswer']);
    Route::post('/exam/submit', [ApiController::class, 'submitExam']);
    Route::get('/results', [ApiController::class, 'getResults']);
    Route::get('/results/{user_id}', [ApiController::class, 'getResults']);

    // Attendance
    Route::get('/attendance/{session_id}', [ApiController::class, 'getAttendanceRecords']);
    Route::get('/my-attendance', [ApiController::class, 'getMyAttendance']);
});

