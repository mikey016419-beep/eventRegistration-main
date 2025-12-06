<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * 活动模型
 * 存储活动的详细信息
 */
class Event extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性，可通过 create() 或 update() 批量赋
     */
    protected $fillable = [
        'user_id',          // 创建者 ID
        'name',             // 活动名称
        'event_time',       // 活动时间
        'location',         // 活动地点
        'max_participants', // 最大参与人数
        'description',      // 活动简介
    ];

    /**
     * 类型转换
     */
    protected $casts = [
        'event_time' => 'datetime',    // 活动时间转为日期时间类型
        'max_participants' => 'integer', // 最大参与人数转为整数
    ];

    /**
     * 活动的创建者（多对一关系）
     * 一个活动属于一个用户创建
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 活动的报名记录（一对多关系）
     * 一个活动可以有多个报名记录
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * 已确认的报名记录（一对多关系）
     * 只返回状态为 confirmed 的报名记录
     */
    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class)->where('status', 'confirmed');
    }

    /**
     * 获取当前已报名人数
     * 
     * @return int
     */
    public function getCurrentParticipantsAttribute(): int
    {
        return $this->confirmedRegistrations()->count();
    }

    /**
     * 判断活动是否已开始
     * 
     * @return bool
     */
    public function isStarted(): bool
    {
        return Carbon::now() >= $this->event_time;
    }

    /**
     * 判断活动是否已结束
     * 
     * @return bool
     */
    public function isEnded(): bool
    {
        // 已截至报名，设定为活动开始前一天
        $endTime = Carbon::parse($this->event_time)->subDays(1);
        return Carbon::now() > $endTime;
    }

    /**
     * 判断活动是否已满员
     * 
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->current_participants >= $this->max_participants;
    }

    /**
     * 判断是否可以报名
     * 
     * @return bool
     */
    public function canRegister(): bool
    {
        // 活动未开始且未满员
        return !$this->isStarted() && !$this->isFull();
    }

    /**
     * 判断是否可以编辑
     * 
     * @return bool
     */
    public function canEdit(): bool
    {
        // 活动未开始
        return !$this->isStarted();
    }

    /**
     * 判断是否可以删除
     * 
     * @return bool
     */
    public function canDelete(): bool
    {
        return !$this->isStarted();
    }
}
