<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;

/**
 * 路由配置
 * 
 * 路由组织结构：
 * - 认证路由：登录、注册、退出
 * - 活动路由：活动列表、详情、创建、编辑、删除（管理员）
 * - 报名路由：报名、取消报名、我的报名记录
 */

// 首页重定向到活动列表
Route::get('/', function () {
    return redirect()->route('events.index');
});

// ==================== 认证路由 ====================

// 显示登录表单（未登录用户可访问）
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');

// 处理登录
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');

// 显示注册表单（未登录用户可访问）
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');

// 处理注册
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');

// 发送邮箱验证链接
Route::post('/email/verification-notification', function (Request $request) {
    if (!$request->user() || $request->user()->hasVerifiedEmail()) {
        return redirect()->back();
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 退出登录（已登录用户可访问）
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ==================== 活动路由 ====================

// 活动列表（所有人可访问）
Route::get('/events', [EventController::class, 'index'])->name('events.index');

// 以下路由需要管理员权限（使用 admin 中间件）
// 注意：具体路由（如 /events/create）必须在参数路由（如 /events/{event}）之前定义
// 否则 Laravel 会把 "create" 当作 {event} 参数来匹配
Route::middleware(['auth', 'admin'])->group(function () {
    // 活动管理页面（仅管理员）
    Route::get('/events/manage', [EventController::class, 'manage'])->name('events.manage');
    
    // 显示创建活动表单（仅管理员）
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    
    // 存储新活动（仅管理员）
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    
    // 显示编辑活动表单（仅管理员）
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    
    // 更新活动（仅管理员）
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    
    // 删除活动（仅管理员）
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // 导出活动报名数据（仅管理员）
    Route::get('/events/{event}/export', [EventController::class, 'export'])->name('events.export');
});

// 活动详情（所有人可访问）
// 注意：这个路由必须在 /events/create 之后，否则会优先匹配
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// ==================== 报名路由 ====================

// 以下路由需要登录（使用 auth 中间件）
Route::middleware('auth')->group(function () {
    // 个人资料（上传头像、修改资料、改密码）
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // 处理报名
    Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
    
    // 取消报名
    Route::post('/registrations/{registration}/cancel', [RegistrationController::class, 'cancel'])->name('registrations.cancel');
    
    // 我的报名记录
    Route::get('/registrations/my', [RegistrationController::class, 'my'])->name('registrations.my');
});
