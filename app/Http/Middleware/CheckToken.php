<?php

namespace App\Http\Middleware;
use Closure;

use \Firebase\JWT\JWT;

class CheckToken
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
        $token = $request->header('Authorization');
        try {
            $result = JWT::decode($token, env('JWTKEY'), array('HS256'));
        } catch (\Exception $e) {
            return response()->json(array('error' => 'Invalid token'));
        }
        
        return $next($request);
    }
}
