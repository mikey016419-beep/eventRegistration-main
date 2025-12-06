<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * 报名模型
 * 存储用户对活动的报名记录
 * 
 * 状态说明：
 * - 'pending': 待确认
 * - 'confirmed': 已确认
 * - 'cancelled': 已取消
 */
class Registration extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'user_id',   // 报名用户 ID
        'event_id',  // 活动 ID
        'phone',     // 联系电话
        'status',    // 报名状态
    ];

    /**
     * 类型转换
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * 报名所属的用户（多对一关系）
     * 一个报名属于一个用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 报名所属的活动（多对一关系）
     * 一个报名属于一个活动
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * 判断是否可以取消报名
     * 活动开始前可以取消
     * 
     * @return bool
     */
    public function canCancel(): bool
    {
        return !$this->event->isStarted() && $this->status === 'confirmed';
    }

    /**
     * 确认报名
     * 
     * @return bool
     */
    public function confirm(): bool
    {
        return $this->update(['status' => 'confirmed']);
    }

    /**
     * 取消报名
     * 
     * @return bool
     */
    public function cancel(): bool
    {
        if ($this->canCancel()) {
            return $this->update(['status' => 'cancelled']);
        }
        return false;
    }
}
