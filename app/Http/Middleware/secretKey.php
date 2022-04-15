<?php

namespace App\Http\Middleware;

use Closure;

class secretKey
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->getClientIp() != $_SERVER['SERVER_ADDR']) return response()->json('Invalid Request');
        return $next($request);
    }
}