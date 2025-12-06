<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 创建活动表
     * 存储活动的详细信息
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // 主键 ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 创建者 ID（外键关联 users 表，级联删除）
            $table->string('name', 50); // 活动名称（最大 50 字符）
            $table->datetime('event_time'); // 活动时间
            $table->string('location', 100); // 活动地点（最大 100 字符）
            $table->integer('max_participants')->unsigned(); // 最大参与人数（非负整数）
            $table->text('description')->nullable(); // 活动简介（可选）
            $table->timestamps(); // 创建时间、更新时间
            
            // 索引优化查询性能
            $table->index('event_time'); // 活动时间索引
            $table->index('user_id'); // 创建者 ID 索引
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
