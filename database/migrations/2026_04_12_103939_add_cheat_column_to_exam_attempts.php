<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Cấy thêm cột đếm số lần gian lận (mặc định là 0)
            if (!Schema::hasColumn('exam_attempts', 'cheat_warnings')) {
                $table->integer('cheat_warnings')->default(0)->after('score');
            }
        });
    }
    public function down() {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn('cheat_warnings');
        });
    }
};