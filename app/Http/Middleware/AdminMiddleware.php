<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('admin')) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập quyền Admin hoặc Giảng viên.');
        }
        return $next($request);
    }
}