<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;               //paquete para recoger los datos por solicitud
use Illuminate\Support\Facades\Validator;  //paquete para validar lo que llega 
use App\Models\User;                       //modelo del usuario
use Illuminate\Support\Facades\Hash;       // paquete para cifrar la contraseña
use App\Helpers\JwtAuth;                   //helper
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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

    public function update(Request $request)
    {
        $token = $request->header('Authorization');                    //obtiene el token del encabezado de la solicitud

        $jwtAuth = new JwtAuth();                        
        $checkToken = $jwtAuth->checkToken($token);                    // se crea una instancia de JwtAuth y se verifica el token 
        $json = $request->input('json', null);                         // Recogemos los datos del formulario en formato JSON
        $params_array = json_decode($json, true);                      // creo un array con los datos y los decodifico

        // verifica si el token es válido
        if ($checkToken) {   
            $user = $jwtAuth->checkToken($token, true);                          // obtener el user identificado
            $id = $user->sub;                                                    // obtiene el ID del usuario desde el token
            $user = User::findOrFail($id);                                       // obiene el usuario en la base de datos desde el id

            $validate = Validator::make($params_array, [                         // valida los datos recibidos
                'name' => 'required',
                'lastname' => 'required',
                'email' => 'email|unique:users,email,' . $user->id,
                'image' => 'image|mimes:jpg,png,jpeg,gif|max:2048'
            ]);

            if (!$validate->fails()) {
                $user->update($params_array);                                      // actualiza los datos del usuario (excepto la imagen)
                

                // si el user cambia la imagen
                if ($request->hasFile('image')) {
                    
                    if ($user->image) {                                  
                        Storage::disk('users')->delete($user->image);               // Elimina imagen anterior si existe
                    }

                    $image = $request->file('image');                               
                    $image_name = time() . '_' . $image->getClientOriginalName();   // Asigna un nombre único
                    Storage::disk('users')->put($image_name, File::get($image));    // Guarda nueva imagen
                    $user->image = $image_name;                                     // Guardar la ruta relativa en la base de datos
                }
                
                $user->save();                                                      // Guardar cambios en la base de datos

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'user' => $user,
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Error al ingresar los datos.'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no esta identificado.'
            ];
        }


        return response()->json($data, $data['code']);
    }
}
