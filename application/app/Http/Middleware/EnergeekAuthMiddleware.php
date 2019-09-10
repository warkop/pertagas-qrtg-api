<?php

namespace App\Http\Middleware;

use App\Http\Models\Users;
use Closure;

class EnergeekAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $idRole = '')
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $access_token = $request->header('access-token') ? $request->header('access-token') : $request->input('access_token');

        if ($access_token) {
            $auth = Users::get_by_access_token($access_token);

            if ($auth) {
                if (!empty($idRole)) {
                    $roles = (strpos($idRole, '&') !== false) ? explode('&', $idRole) : array($idRole);

                    if (in_array($auth->role, $roles)) {
                        $responseCode = 200;
                    } else {
                        $responseCode = 403;
                        $responseMessage = 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator';
                    }
                } else {
                    $responseCode = 200;
                }
            } else {
                $responseCode = 401;
                $responseMessage = 'Token tidak valid';
            }
        } else {
            $responseCode = 403;
            $responseMessage = 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator';
        }

        if ($responseCode == 200) {
            return $next($request);
        } else {
            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
            return response()->json($response, $responseCode);
        }
    }
}
