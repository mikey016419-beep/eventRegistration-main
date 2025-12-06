<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 确保用户是管理员的中间件
 * 用于检查用户是否有管理员权限
 */
class EnsureUserIsAdmin
{
    /**
     * 处理传入的请求
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 检查用户是否已登录
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 检查用户是否为管理员
        if (!auth()->user()->isAdmin()) {
            abort(403, '只有管理员可以访问此页面');
        }

        return $next($request);
    }
}
