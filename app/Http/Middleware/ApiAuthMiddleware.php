<?php

namespace App\Http\Middleware;

use APP\Helpers\JwtAuth;
use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if($checkToken){
            return $next($request);
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Es nesesario inisiar sesion',
            );
            return response()->json($data, $data['code']);
        }
    }
}
