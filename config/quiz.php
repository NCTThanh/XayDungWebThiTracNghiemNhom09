<?php

return [
    // Feature Toggles
    'registration_enabled' => env('QUIZ_REGISTRATION_ENABLED', true),
    'email_verification' => env('QUIZ_EMAIL_VERIFICATION', true),
    'open_quiz' => env('QUIZ_OPEN_QUIZ', false),
    'webcam_monitoring' => env('QUIZ_WEBCAM_MONITORING', true),
    'google_charts' => env('QUIZ_GOOGLE_CHARTS', true),
    
    // Security
    'master_password' => env('QUIZ_MASTER_PASSWORD', 'Admin@123456'),
    'file_max_size' => env('FILE_MAX_SIZE', 10240), // KB
    'file_allowed_extensions' => env('FILE_ALLOWED_EXTENSIONS', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx'),
    
    // Localization
    'default_group' => env('QUIZ_DEFAULT_GROUP', 1),
    'base_currency' => env('QUIZ_BASE_CURRENCY', 'VND'),
    'default_timezone' => env('QUIZ_DEFAULT_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    'default_language' => env('QUIZ_DEFAULT_LANGUAGE', 'vi'),
    
    // Payment Gateways
    'gateways' => [
        'vnpay' => [
            'enabled' => !empty(env('VNPAY_TMNCODE')),
            'tmncode' => env('VNPAY_TMNCODE'),
            'hashsecret' => env('VNPAY_HASHSECRET'),
            'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paygate'),
        ],
        'momo' => [
            'enabled' => !empty(env('MOMO_PARTNER_CODE')),
            'partner_code' => env('MOMO_PARTNER_CODE'),
            'access_key' => env('MOMO_ACCESS_KEY'),
            'secret_key' => env('MOMO_SECRET_KEY'),
            'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        ],
        'stripe' => [
            'enabled' => !empty(env('STRIPE_PUBLIC_KEY')),
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        'paypal' => [
            'enabled' => !empty(env('PAYPAL_CLIENT_ID')),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ],
        '2checkout' => [
            'enabled' => !empty(env('2CHECKOUT_MERCHANT_CODE')),
            'merchant_code' => env('2CHECKOUT_MERCHANT_CODE'),
            'merchant_key' => env('2CHECKOUT_MERCHANT_KEY'),
            'api_url' => env('2CHECKOUT_API_URL', 'https://sandbox.2checkout.com'),
        ],
    ],
    
    // Firebase
    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'database_url' => env('FIREBASE_DATABASE_URL'),
        'private_key' => env('FIREBASE_PRIVATE_KEY'),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
        'client_email' => env('FIREBASE_CLIENT_EMAIL'),
        'client_id' => env('FIREBASE_CLIENT_ID'),
        'auth_uri' => env('FIREBASE_AUTH_URI'),
        'token_uri' => env('FIREBASE_TOKEN_URI'),
    ],
    
    // AI Services
    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'),
        'gemini_api_key' => env('GEMINI_API_KEY'),
    ],
    
    // Email Templates
    'email_templates' => [
        'activation' => env('MAIL_ACTIVATION_TEMPLATE', 'activation'),
        'password_reset' => env('MAIL_PASSWORD_RESET_TEMPLATE', 'password_reset'),
        'result' => env('MAIL_RESULT_TEMPLATE', 'quiz_result'),
    ],
    
    // Android App / API
    'android' => [
        'api_enabled' => env('ANDROID_API_ENABLED', true),
        'app_secret' => env('ANDROID_APP_SECRET'),
    ],
    
    // API Rate Limiting
    'api_rate_limit' => env('API_RATE_LIMIT', 60),
    'api_rate_limit_period' => env('API_RATE_LIMIT_PERIOD', 1),
];
