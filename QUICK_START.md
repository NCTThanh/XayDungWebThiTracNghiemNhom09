# Quick Start Guide - Laravel 12 Quiz System

## Installation

### 1. Setup Database
```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
# DB_HOST=localhost
# DB_DATABASE=quiz_system
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seeding
php artisan migrate --seed
```

### 2. Configure Services

#### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password  # Use Gmail App Password
MAIL_FROM_ADDRESS=noreply@quizsystem.com
```

#### Payment Gateways (Choose at least one)
```env
# VNPay (Vietnam)
VNPAY_TMNCODE=your_code
VNPAY_HASHSECRET=your_secret

# Momo (Vietnam Mobile Payment)
MOMO_PARTNER_CODE=your_code
MOMO_ACCESS_KEY=your_key
MOMO_SECRET_KEY=your_secret

# Stripe (International)
STRIPE_PUBLIC_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx

# PayPal
PAYPAL_CLIENT_ID=xxx
PAYPAL_CLIENT_SECRET=xxx
```

#### Firebase (For Push Notifications)
```env
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_DATABASE_URL=your_db_url
FIREBASE_PRIVATE_KEY=your_private_key
FIREBASE_CLIENT_EMAIL=your_service_account_email
```

#### AI Services (For Question Generation)
```env
GEMINI_API_KEY=your_gemini_api_key
AI_PROVIDER=gemini
```

### 3. Start Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## Default Login Credentials

### Admin Panel
- **URL**: http://localhost:8000/admin
- **Username**: tuyen (or: ha, thuys, kimngan, ban)
- **Password**: 123456

### Test Student Accounts
- **Email**: a@gmail.com
- **Password**: 123456 (MD5 hash in seed)

---

## Key Features Walkthrough

### User Registration & Login
1. Go to homepage
2. Click "Register" to create account
3. Verify email (if enabled)
4. Login with credentials

### Create Quiz (Admin)
1. Go to /admin
2. Click "Quizzes"
3. Click "Create New Quiz"
4. Add questions and options
5. Set passing score and duration
6. Publish quiz

### Take Exam (Student)
1. Login as student
2. Go to dashboard
3. Select quiz
4. Click "Start Exam"
5. Answer questions
6. Submit exam
7. View results

### Payment Integration
Students can purchase premium quizzes using any of the 4 integrated payment gateways

### API Usage (Android/Mobile App)

#### 1. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "a@gmail.com",
    "password": "123456"
  }'
```

Response:
```json
{
  "success": true,
  "user": {...},
  "api_key": "api_xxx...",
  "expires_in": "never"
}
```

#### 2. Use API Key for All Requests
```bash
curl -X GET http://localhost:8000/api/quizzes \
  -H "X-API-Key: api_xxx..." \
  -H "Content-Type: application/json"
```

#### 3. Available Endpoints
```
GET    /api/quizzes                  # List all quizzes
GET    /api/quizzes/{id}             # Get single quiz
POST   /api/exam/start               # Start exam
POST   /api/exam/submit-answer       # Submit one answer
POST   /api/exam/submit              # Complete exam
GET    /api/results                  # Get my results
GET    /api/user                     # Get my profile
PUT    /api/user/profile             # Update profile
```

---

## AI Question Generation

### Using Gemini API

```php
use App\Services\AiQuestionService;

$ai = new AiQuestionService();

// Generate 5 multiple choice questions
$questions = $ai->generateMultipleChoice(
    topic: 'Biology',
    quantity: 5,
    difficulty: 'medium'
);

// Generate 3 true/false questions
$tfQuestions = $ai->generateTrueFalse(
    topic: 'History',
    quantity: 3,
    difficulty: 'easy'
);

// Evaluate essay answer
$evaluation = $ai->evaluateEssayAnswer(
    question: 'What is photosynthesis?',
    studentAnswer: 'Student answer text...'
);

// Output
echo $evaluation['score'];      // 0-100
echo $evaluation['feedback'];   // String feedback
```

---

## Push Notifications

### Register Device Token
```javascript
// From frontend JavaScript
fetch('/api/firebase-token', {
    method: 'POST',
    headers: {'X-API-Key': apiKey},
    body: JSON.stringify({
        token: fcmToken,
        device_type: 'web'
    })
});
```

### Send as Admin
```php
use App\Services\FirebaseService;

// To specific user
FirebaseService::sendToUser(userId: 5, 
    title: 'Quiz Available', 
    body: 'New quiz has been published'
);

// To all users with role
FirebaseService::notifyByRole('student',
    title: 'Exam Results',
    body: 'Your exam has been graded'
);

// Notify on quiz completion
FirebaseService::notifyQuizResult(
    userId: 5,
    quizTitle: 'Math Final',
    score: 85.5,
    isPassed: true
);
```

---

## Database Relationships

```
User
├─ hasMany → ExamAttempt
├─ hasMany → Payment
├─ hasMany → Subscription
├─ hasMany → FirebaseToken
├─ hasMany → ApiKey
├─ hasMany → ActivityLog
└─ belongsTo → Group

Payment
├─ belongsTo → User
└─ belongsTo → Quiz

Subscription
└─ belongsTo → User

ApiKey
└─ belongsTo → User
```

---

## Email Templates

Email templates are stored in database (`email_templates` table).

Available variables:
- `{{name}}` - User name
- `{{email}}` - User email
- `{{url}}` - Action URL
- `{{quiz_title}}` - Quiz title
- `{{score}}` - Score
- `{{passed}}` - Pass/fail status

Edit templates in admin panel or database.

---

## Security Configuration

### Enabled Security Features
✅ HTTP Security Headers
✅ CSRF Protection
✅ Password Hashing (Bcrypt)
✅ Input Validation
✅ File Upload Restrictions
✅ Config File Protection (.htaccess)
✅ Activity Logging
✅ API Key Authentication
✅ Rate Limiting (Ready)

### Plesk Hosting Compatibility
- Automatic rewrite rules setup
- .htaccess protects config files
- Compatible with FastCGI, PHP, SSI

---

## Troubleshooting

### Email Not Sending
1. Check `.env` mail configuration
2. Verify SMTP credentials
3. Check `storage/logs/laravel.log`
4. Enable "Less secure apps" if using Gmail

### Payment Gateway Errors
1. Verify gateway credentials in `.env`
2. Check transaction logs in database
3. Review payment gateway documentation

### Firebase Notifications Not Working
1. Verify Firebase credentials
2. Check FCM token is valid
3. Review service account permissions

### API Key Invalid
1. Generate new key: `POST /api/api-key/generate`
2. Include `X-API-Key` header in all requests
3. Check key is marked as active

---

## File Locations

```
Important Files:
- Authentication: app/Http/Controllers/AuthController.php
- Admin Panel: app/Http/Controllers/AdminController.php
- Payments: app/Http/Controllers/PaymentController.php
- API: app/Http/Controllers/Api/ApiController.php
- Firebase: app/Services/FirebaseService.php
- AI: app/Services/AiQuestionService.php
- Config: config/quiz.php
- Security: public/.htaccess
- Documentation: IMPLEMENTATION_SUMMARY.md
```

---

## Production Deployment

```bash
# 1. Set production environment
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env

# 2. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Run final migrations
php artisan migrate --force

# 4. Clear caches
php artisan cache:clear
php artisan view:clear
```

---

## Support & Additional Resources

- Email: noreply@quizsystem.com
- Admin Dashboard: http://localhost:8000/admin
- API Docs: Available at individual endpoint documentation

---

**Last Updated**: April 11, 2026
**Framework**: Laravel 12
**Status**: Production Ready ✅
