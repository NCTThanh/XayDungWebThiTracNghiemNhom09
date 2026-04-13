<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\FirebaseToken;
use App\Models\User;

class FirebaseService {
    
    private static $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    private static $firebaseMessagingUrl = 'https://fcm.googleapis.com/v1/projects';

    // ===================================
    // SEND NOTIFICATIONS
    // ===================================

    /**
     * Send notification to single user
     */
    public static function sendToUser($userId, $title, $body, $data = []) {
        try {
            $user = User::find($userId);
            if (!$user) return false;

            $tokens = $user->firebaseTokens()->pluck('token')->toArray();
            if (empty($tokens)) return false;

            return self::sendToMultiple($tokens, $title, $body, $data);
        } catch (\Exception $e) {
            Log::error('SendToUser error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple tokens
     */
    public static function sendToMultiple($tokens, $title, $body, $data = []) {
        if (empty($tokens)) return false;

        $successCount = 0;
        foreach ($tokens as $token) {
            if (self::sendPushNotification($token, $title, $body, null, $data)) {
                $successCount++;
            }
        }

        return $successCount > 0;
    }

    /**
     * Send push notification using FCM HTTP API v1
     */
    public static function sendPushNotification($deviceToken, $title, $body, $actionUrl = null, $data = []) {
        if (empty($deviceToken)) return false;

        // Try Firebase HTTP API v1 first
        $result = self::sendViaFirebaseV1($deviceToken, $title, $body, $actionUrl, $data);
        
        // Fallback to FCM HTTP API if v1 fails
        if (!$result) {
            $result = self::sendViaFCM($deviceToken, $title, $body, $actionUrl, $data);
        }

        return $result;
    }

    /**
     * Send via Firebase Messaging API v1
     */
    private static function sendViaFirebaseV1($token, $title, $body, $actionUrl = null, $data = []) {
        try {
            $projectId = config('quiz.firebase.project_id');
            if (!$projectId) return false;

            $accessToken = self::getAccessToken();
            if (!$accessToken) return false;

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'webpush' => [
                        'fcmOptions' => [
                            'link' => $actionUrl ?? url('/')
                        ]
                    ]
                ]
            ];

            if (!empty($data)) {
                $payload['message']['data'] = $data;
            }

            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post(
                self::$firebaseMessagingUrl . "/{$projectId}/messages:send",
                $payload
            );

            return $response->successful();
        } catch (\Exception $e) {
            Log::debug('Firebase v1 send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via Legacy FCM HTTP API
     */
    private static function sendViaFCM($token, $title, $body, $actionUrl = null, $data = []) {
        try {
            $serverKey = env('FIREBASE_SERVER_KEY');
            if (!$serverKey) {
                // Fallback: try to extract from private key or use default
                $serverKey = self::getServerKey();
            }

            if (!$serverKey) return false;

            $payload = [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => url('/images/logo.png'),
                    'click_action' => $actionUrl ?? url('/'),
                    'sound' => 'default'
                ],
                'priority' => 'high'
            ];

            if (!empty($data)) {
                $payload['data'] = $data;
            }

            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post(self::$fcmUrl, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::warning('FCM send failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('FCM HTTP error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get access token for Firebase v1 API
     */
    private static function getAccessToken() {
        try {
            $cacheKey = 'firebase_access_token';
            
            // Check cache first
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $firebaseConfig = config('quiz.firebase');
            if (empty($firebaseConfig['private_key']) || empty($firebaseConfig['client_email'])) {
                return null;
            }

            // Create JWT token
            $privateKey = str_replace('\\n', "\n", $firebaseConfig['private_key']);
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            
            $now = time();
            $claimsSet = [
                'iss' => $firebaseConfig['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now
            ];
            
            $claims = base64_encode(json_encode($claimsSet));
            
            // Sign JWT
            $signature = '';
            openssl_sign("{$header}.{$claims}", $signature, $privateKey, 'SHA256');
            $signature = base64_encode($signature);
            
            $jwt = "{$header}.{$claims}.{$signature}";

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->successful()) {
                $token = $response->json()['access_token'];
                Cache::put($cacheKey, $token, 3500); // Cache for ~1 hour
                return $token;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Get access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get server key from private key or env
     */
    private static function getServerKey() {
        // The server key is now deprecated, but we keep it for backward compatibility
        return env('FIREBASE_SERVER_KEY', '');
    }


    // ===================================
    // TOKEN MANAGEMENT
    // ===================================

    /**
     * Register or update device token for user
     */
    public static function registerToken($userId, $token, $deviceType = 'web') {
        try {
            if (empty($token) || empty($userId)) return false;

            return FirebaseToken::updateOrCreate(
                ['user_id' => $userId, 'token' => $token],
                ['device_type' => $deviceType, 'updated_at' => now()]
            );
        } catch (\Exception $e) {
            Log::error('Firebase token registration error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove device token
     */
    public static function removeToken($token) {
        try {
            if (empty($token)) return false;
            return FirebaseToken::where('token', $token)->delete();
        } catch (\Exception $e) {
            Log::error('Firebase token removal error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if token is valid for user
     */
    public static function isTokenValid($userId, $token) {
        try {
            return FirebaseToken::where('user_id', $userId)
                ->where('token', $token)
                ->exists();
        } catch (\Exception $e) {
            Log::error('Token validation error: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================
    // BULK NOTIFICATIONS
    // ===================================

    /**
     * Send notification to all users with tokens
     */
    public static function notifyAllUsers($title, $body, $data = []) {
        try {
            $users = User::whereHas('firebaseTokens')->limit(100)->get();
            
            $count = 0;
            foreach ($users as $user) {
                if (self::sendToUser($user->id, $title, $body, $data)) {
                    $count++;
                }
            }
            
            return $count;
        } catch (\Exception $e) {
            Log::error('Notify all users error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Send notification to users by role
     */
    public static function notifyByRole($role, $title, $body, $data = []) {
        try {
            $users = User::where('role', $role)
                ->whereHas('firebaseTokens')
                ->limit(100)
                ->get();
            
            $count = 0;
            foreach ($users as $user) {
                if (self::sendToUser($user->id, $title, $body, $data)) {
                    $count++;
                }
            }
            
            return $count;
        } catch (\Exception $e) {
            Log::error('Notify by role error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Send notification to users by group
     */
    public static function notifyByGroup($groupId, $title, $body, $data = []) {
        try {
            $users = User::where('group_id', $groupId)
                ->whereHas('firebaseTokens')
                ->limit(100)
                ->get();
            
            $count = 0;
            foreach ($users as $user) {
                if (self::sendToUser($user->id, $title, $body, $data)) {
                    $count++;
                }
            }
            
            return $count;
        } catch (\Exception $e) {
            Log::error('Notify by group error: ' . $e->getMessage());
            return 0;
        }
    }

    // ===================================
    // SPECIAL NOTIFICATIONS
    // ===================================

    /**
     * Send quiz result notification
     */
    public static function notifyQuizResult($userId, $quizTitle, $score, $isPassed) {
        try {
            $title = "Kết quả bài thi";
            $body = $quizTitle . " - Điểm: " . round($score, 2) . " (" . ($isPassed ? "✓ Đạt" : "✗ Chưa đạt") . ")";
            
            return self::sendToUser($userId, $title, $body, [
                'type' => 'quiz_result',
                'score' => (string) round($score, 2),
                'passed' => $isPassed ? '1' : '0'
            ]);
        } catch (\Exception $e) {
            Log::error('Quiz result notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send quiz published notification
     */
    public static function notifyQuizPublished($quizTitle, $data = []) {
        try {
            return self::notifyByRole('student', 
                'Thông báo bài thi mới', 
                'Đề thi "' . $quizTitle . '" đã được công bố',
                array_merge($data, ['type' => 'quiz_published'])
            );
        } catch (\Exception $e) {
            Log::error('Quiz published notification error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Send payment success notification
     */
    public static function notifyPaymentSuccess($userId, $amount, $currency = 'VND') {
        try {
            return self::sendToUser($userId, 
                'Thanh toán thành công',
                'Bạn đã thanh toán ' . number_format($amount, 0, ',', '.') . ' ' . $currency,
                ['type' => 'payment_success', 'amount' => (string) $amount]
            );
        } catch (\Exception $e) {
            Log::error('Payment notification error: ' . $e->getMessage());
            return false;
        }
    }
}
