<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
use App\Models\ApiKey;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller {
    
    protected $user;

    public function __construct() {
        $this->middleware('api.key');
    }

    // ===================================
    // AUTHENTICATION ENDPOINTS
    // ===================================

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Check password (support both bcrypt and MD5)
        $passwordValid = Hash::check($request->password, $user->password) || 
                        $user->password === md5($request->password);

        if (!$passwordValid) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate API token
        $apiKey = ApiKey::generateKey($user->id, 'mobile_app_' . time());

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'group_id' => $user->group_id
            ],
            'api_key' => $apiKey->key,
            'expires_in' => 'never'
        ]);
    }

    public function generateApiKey(Request $request) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $apiKey = ApiKey::generateKey($user->id, $request->app_name);

        return response()->json([
            'success' => true,
            'api_key' => $apiKey->key,
            'app_name' => $apiKey->app_name,
            'created_at' => $apiKey->created_at
        ]);
    }

    // ===================================
    // QUIZ ENDPOINTS
    // ===================================

    public function listQuizzes(Request $request) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $quizzes = Quiz::where('is_published', true)
            ->with('questions')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $quizzes->items(),
            'pagination' => [
                'total' => $quizzes->total(),
                'per_page' => $quizzes->perPage(),
                'current_page' => $quizzes->currentPage(),
                'last_page' => $quizzes->lastPage()
            ]
        ]);
    }

    public function getQuiz(Request $request, $quizId) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $quiz = Quiz::with(['questions.options'])->find($quizId);

        if (!$quiz || !$quiz->is_published) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }

        // Check if user has access (free or purchased)
        if (!$this->userHasQuizAccess($user, $quiz)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $quiz
        ]);
    }

    // ===================================
    // EXAM ATTEMPT ENDPOINTS
    // ===================================

    public function startExam(Request $request) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $quiz = Quiz::find($request->quiz_id);

        if (!$quiz) {
            return response()->json(['error' => 'Quiz not found'], 404);
        }

        // Check access
        if (!$this->userHasQuizAccess($user, $quiz)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check previous attempts
        $existingAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', '!=', 'completed')
            ->first();

        if ($existingAttempt) {
            return response()->json([
                'success' => true,
                'attempt_id' => $existingAttempt->id,
                'message' => 'Resuming existing attempt'
            ]);
        }

        // Create new attempt
        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'status' => 'in_progress',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'message' => 'Exam started'
        ]);
    }

    public function submitAnswer(Request $request) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'attempt_id' => 'required|exists:exam_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:options,id',
            'text_answer' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update or create answer
        $attemptAnswer = \App\Models\AttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $request->attempt_id,
                'question_id' => $request->question_id
            ],
            [
                'option_id' => $request->option_id,
                'text_answer' => $request->text_answer
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Answer saved'
        ]);
    }

    public function submitExam(Request $request) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $attempt = ExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Attempt not found'], 404);
        }

        // Calculate score
        $score = $this->calculateScore($attempt);

        // Update attempt
        $attempt->update([
            'status' => 'completed',
            'end_time' => now(),
            'score' => $score
        ]);

        return response()->json([
            'success' => true,
            'score' => $score,
            'message' => 'Exam submitted successfully'
        ]);
    }

    public function getResults(Request $request, $userId = null) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Only allow users to see their own results or admins to see anyone's
        $targetUserId = $userId ?? $user->id;
        if ($targetUserId != $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $results = ExamAttempt::where('user_id', $targetUserId)
            ->where('status', 'completed')
            ->with('quiz')
            ->orderBy('end_time', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $results->items(),
            'pagination' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage()
            ]
        ]);
    }

    // ===================================
    // USER PROFILE ENDPOINTS
    // ===================================

    public function getProfile(Request $request) {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'student_code' => $user->student_code,
                'class' => $user->class,
                'role' => $user->role,
                'group_id' => $user->group_id,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at
            ]
        ]);
    }

    public function updateProfile(Request $request) {
        /** @var User $user */
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'old_password' => 'required_with:password',
            'password' => 'sometimes|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password) && 
                $user->password !== md5($request->old_password)) {
                return response()->json(['error' => 'Current password is incorrect'], 422);
            }
            
            $user->password = Hash::make($request->password);
        }

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    // ===================================
    // ATTENDANCE ENDPOINTS
    // ===================================

    /**
     * Get attendance records for a session
     */
    public function getAttendanceRecords($sessionId)
    {
        try {
            $session = \App\Models\AttendanceSession::find($sessionId);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Check if user is the creator or admin
            if (auth('api')->user()->id !== $session->created_by && auth('api')->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $attendees = $session->records()->with(['user'])->get()->map(function($record) {
                return [
                    'id' => $record->user->id,
                    'name' => $record->user->name,
                    'email' => $record->user->email,
                    'student_code' => $record->user->student_code,
                    'scan_time' => $record->scan_time,
                    'status' => $record->status
                ];
            });

            return response()->json([
                'success' => true,
                'count' => $attendees->count(),
                'attendees' => $attendees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user's attendance records
     */
    public function getMyAttendance()
    {
        try {
            $user = auth('api')->user();
            $records = $user->attendanceRecords()
                ->with(['session', 'session.quiz', 'session.creator'])
                ->orderBy('scan_time', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'count' => $records->count(),
                'records' => $records->map(function($record) {
                    return [
                        'id' => $record->id,
                        'quiz_title' => $record->session->quiz->title ?? 'N/A',
                        'teacher_name' => $record->session->creator->name ?? 'N/A',
                        'scan_time' => $record->scan_time,
                        'status' => $record->status
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===================================
    // HELPER METHODS
    // ===================================

    private function userHasQuizAccess($user, $quiz) {
        // Admin always has access
        if ($user->role === 'admin') {
            return true;
        }

        // Check if quiz is free
        if (!$quiz->is_paid) {
            return true;
        }

        // Check if user purchased or has subscription
        if ($user->hasPremium() || $user->purchased_quizzes()->where('quiz_id', $quiz->id)->exists()) {
            return true;
        }

        return false;
    }

    private function calculateScore($attempt) {
        $questions = $attempt->attempt_answers;
        $totalMarks = $attempt->quiz->questions->sum('marks');
        $earnedMarks = 0;

        foreach ($questions as $answer) {
            if ($answer->is_correct) {
                $earnedMarks += $answer->earned_marks;
            }
        }

        return ($earnedMarks / $totalMarks) * 100;
    }
}
