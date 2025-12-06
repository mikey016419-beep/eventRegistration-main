<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 创建报名表
     * 存储用户对活动的报名记录
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id(); // 主键 ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 报名用户 ID（外键关联 users 表，级联删除）
            $table->foreignId('event_id')->constrained()->onDelete('cascade'); // 活动 ID（外键关联 events 表，级联删除）
            $table->string('phone', 11); // 联系电话（11 位手机号）
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending'); // 报名状态：pending=待确认, confirmed=已确认, cancelled=已取消
            $table->timestamps(); // 创建时间、更新时间
            
            // 唯一约束：同一用户不能重复报名同一活动
            $table->unique(['user_id', 'event_id']);
            
            // 索引优化查询性能
            $table->index('event_id'); // 活动 ID 索引
            $table->index('status'); // 状态索引
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
