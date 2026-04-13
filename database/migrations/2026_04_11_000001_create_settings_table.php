<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 1. Settings table
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type', 50)->default('string');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. User groups/roles
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Add group_id to users
        if (!Schema::hasColumn('users', 'group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('group_id')->nullable()->default(1)->after('role');
            });
        }

        // 4. Subscriptions
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // Bỏ khóa ngoại cứng để an toàn
                $table->string('plan_name', 100);
                $table->float('amount')->default(0);
                $table->string('status', 20)->default('active'); 
                $table->timestamp('start_date');
                $table->timestamp('end_date')->nullable();
                $table->timestamps();
            });
        }

        // 5. Payments
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable(); // Bỏ khóa ngoại cứng
                $table->unsignedBigInteger('quiz_id')->nullable(); // Bỏ khóa ngoại cứng
                $table->string('gateway', 50); 
                $table->string('transaction_id')->unique();
                $table->float('amount');
                $table->string('currency', 10)->default('VND');
                $table->string('status', 20)->default('pending'); 
                $table->text('response_data')->nullable();
                $table->timestamps();
            });
        }

        // 6. Firebase Tokens (Đã sửa lỗi copy nhầm tên bảng)
        if (!Schema::hasTable('firebase_tokens')) {
            Schema::create('firebase_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); 
                $table->string('token');
                $table->timestamps();
                $table->unique(['user_id', 'token']); 
            });
        }

        // 7. API Keys
        if (!Schema::hasTable('api_keys')) {
            Schema::create('api_keys', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // Bỏ khóa ngoại cứng
                $table->string('key')->unique();
                $table->string('app_name', 100);
                $table->text('permissions')->nullable(); 
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
            });
        }

        // 8. Email templates
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('subject', 255);
                $table->text('body');
                $table->text('variables')->nullable(); 
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 9. Proctoring logs
        if (!Schema::hasTable('proctoring_logs')) {
            Schema::create('proctoring_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attempt_id'); // Bỏ khóa ngoại cứng
                $table->text('photo_path')->nullable();
                $table->text('violation_details')->nullable(); 
                $table->string('status', 20)->default('normal'); 
                $table->timestamps();
            });
        }

        // 10. Activity logs
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable(); // Bỏ khóa ngoại cứng
                $table->string('action', 100);
                $table->string('model', 100)->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->text('details')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down() {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('proctoring_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('firebase_tokens');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
        
        if (Schema::hasColumn('users', 'group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('group_id');
            });
        }
        
        Schema::dropIfExists('groups');
        Schema::dropIfExists('settings');
    }
};