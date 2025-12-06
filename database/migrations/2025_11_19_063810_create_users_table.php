<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * 创建用户表
     * 包含认证所需的基础字段和角色字段
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // 主键 ID
            $table->string('name'); // 用户名
            $table->string('email')->unique(); // 邮箱（唯一）
            $table->timestamp('email_verified_at')->nullable(); // 邮箱验证时间
            $table->string('password'); // 密码（加密存储）
            $table->enum('role', ['user', 'admin'])->default('user'); // 角色：user=普通用户, admin=管理员
            $table->rememberToken(); // 记住我功能的 token
            $table->timestamps(); // 创建时间、更新时间
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
