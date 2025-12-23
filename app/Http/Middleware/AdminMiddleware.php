<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();


        if ($user->account_type !== 'admin') {
            abort(403, 'غير مصرح لك بالدخول');
        }

      
        if ($user->status !== 'approved' || !$user->is_active) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'حسابك غير مفعل');
        }

        return $next($request);
    }
}
