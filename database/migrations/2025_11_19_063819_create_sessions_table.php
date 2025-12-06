<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 创建会话表
     * Laravel 用于存储用户会话信息
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // 会话 ID（主键）
            $table->foreignId('user_id')->nullable()->index(); // 用户 ID（可空，索引）
            $table->string('ip_address', 45)->nullable(); // IP 地址
            $table->text('user_agent')->nullable(); // 用户代理
            $table->longText('payload'); // 会话数据
            $table->integer('last_activity')->index(); // 最后活动时间（索引）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
