<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;               //paquete para recoger los datos por solicitud
use Illuminate\Support\Facades\Validator;  //paquete para validar lo que llega 
use App\Models\User;                       //modelo del usuario
use Illuminate\Support\Facades\Hash;       // paquete para cifrar la contraseña
use App\Helpers\JwtAuth;                   //helper

class UserController extends Controller
{
    public function login(Request $request)
    {
        $JwtAuth = new JwtAuth();                       // creo el helper   
        $json = $request->input('json', null);          // Recogemos los datos del formulario en formato JSON
        $params = json_decode($json);                   // se transforma en objeto php
        $params_array = json_decode($json, true);       // se transforma en array

        // Validamos los datos
        $validate = Validator::make($params_array, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8'
        ]);

        // Si la validación falla
        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Error, el usuario no se ha podido loguear.',
                'errors' => $validate->errors()
            ], 400);
        }

        // Buscar usuario en la base de datos
        $user = User::where('email', $params->email)->first();

        // Verificar si la contraseña es incorrecta
        if (!Hash::check($params->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'La contraseña es incorrecta.'
            ], 401);
        }

        // Intentamos realizar el login
        if (isset($params->email) && isset($params->password)) {
            $getToken = isset($params->getToken) ? $params->getToken : null;           // se verifica si se ha enviado el token 
            $signup = $JwtAuth->signup($params->email, $getToken);  // Llamada a la función de autenticación para generar el token

            return response()->json($signup, 200);
        }

        // Si faltan las credenciales
        return response()->json([
            'status' => 'error',
            'code' => 400,
            'message' => 'Los datos proporcionados son incompletos.'
        ], 400);
    }
}
