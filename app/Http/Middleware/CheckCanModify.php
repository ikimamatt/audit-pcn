<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\AuthHelper;

class CheckCanModify
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!AuthHelper::canModifyData()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan perubahan data.'
                ], 403);
            }
            abort(403, 'Anda tidak memiliki akses untuk melakukan perubahan data.');
        }

        return $next($request);
    }
}
