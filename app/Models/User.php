<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 用户模型
 * 用于用户认证和授权
 * 
 * 角色说明：
 * - 'user': 普通用户（参与者）
 * - 'admin': 管理员（活动组织者）
 */
class User extends AuthenticatableUser
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'name',      // 用户名
        'email',     // 邮箱
        'password',  // 密码（会自动加密）
        'role',      // 角色：user 或 admin
        'avatar',    // 头像路径
    ];

    /**
     * 应该被隐藏的属性（序列化时不显示）
     */
    protected $hidden = [
        'password',        // 密码
        'remember_token',  // 记住我令牌
    ];

    /**
     * 类型转换
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // 邮箱验证时间转为日期时间类型
        'password' => 'hashed',            // 密码自动加密
    ];

    /**
     * 用户创建的活动（一对多关系）
     * 一个用户可以创建多个活动
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * 用户的报名记录（一对多关系）
     * 一个用户可以有多个报名记录
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * 判断用户是否为管理员
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * 判断用户是否为普通用户
     * 
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
