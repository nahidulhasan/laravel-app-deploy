<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use App\Services\ClientCredentialService;



class VerifyClientSecret
{
    use ApiResponse;


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            if (!$request->hasHeader('client-secret')) {
                return $this->errorResponse("Token missing", 404);
            }

            if (!$this->isValidToken($request)) {
                return $this->errorResponse("Token is invalid", 404);
            }

            return $next($request);

        } catch (Exception $e) {

            return $this->errorResponse($e->getMessage(), 404);
        }

       // return $next($request);
    }


    /**
     * @param $request
     * @return bool
     */
    private function isValidToken($request)
    {
        $token = $request->header('client-secret');

        $token = ClientCredentialService::isValidToken($token);

        return $token;
    }

}
