<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');                           // Obteniene el token del encabezado de autorización de la solicitud.
        $jwtAuth = new JwtAuth();                                             // Creá una instancia de JwtAuth para manejar la autenticación JWT.
        $checkToken = $jwtAuth->checkToken($token);                           // Verificá si el token es válido.

        if($checkToken) {                
            return $next($request);                                          // Si el token es válido, permite que la solicitud continúe con el controlador.
        } else {
            $data = [                                                        // Si el token no es válido, se prepara un mensaje de error.
                'status' => 'error',
                'code' => 400,
                'message' => 'Error, el usuario no esta identificado.'
            ];
        }

        return response()->json($data, $data['code']);                      // Devuelve una respuesta JSON con el mensaje de error y el código de estado correspondiente.
        
    }
}
