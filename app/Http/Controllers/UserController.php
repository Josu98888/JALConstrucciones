<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;               //paquete para recoger los datos por solicitud
use Illuminate\Support\Facades\Validator;  //paquete para validar lo que llega 
use App\Models\User;                       //modelo del usuario
use Illuminate\Support\Facades\Hash;       // paquete para cifrar la contraseña
use App\Helpers\JwtAuth;                   //helper
use Illuminate\Support\Facades\File;       //paquete para trabajar con archivos
use Illuminate\Support\Facades\Storage;    //paquete para almacenar archivos

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

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'El usuario se ha logueado correctamente.',
                'token' => $signup,
                'user' => $user
            ];
            return response()->json($data, $data['code']);                               // Retorna la respuesta en formato JSON con el código de estado correspondiente.
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
        
        // verifica si el token es válido
        if ($checkToken) {   
            $user = $jwtAuth->checkToken($token, true);                          // obtener el user identificado
            $id = $user->sub;                                                    // obtiene el ID del usuario desde el token
            $user = User::findOrFail($id);                                       // obiene el usuario en la base de datos desde el id

            $validate = Validator::make($request->all(), [                         // valida los datos recibidos
                'name' => 'required',
                'lastname' => 'required',
                'email' => 'email|unique:users,email,' . $user->id,
                'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048'
            ]);

            if (!$validate->fails()) {
                $user->fill($request->except(['image']));                                     // actualiza los datos del usuario (excepto la imagen)
                

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
                    'errors' => $validate->errors() ,
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
    

    public function getImage($filename)
    {
            $path = storage_path("app/public/users/{$filename}");                    // Construye la ruta completa donde se encuentra la imagen en el almacenamiento.

            // Verifica si el archivo existe en la ruta especificada.
            if (File::exists($path)) {
                $file = File::get($path);                                            // Obtiene el contenido del archivo.
                $mimeType = File::mimeType($path);                                   // Obtiene el tipo MIME del archivo para indicar correctamente el tipo de contenido.
        
                return response($file, 200)->header("Content-Type", $mimeType);      // Retorna la imagen con un código de respuesta 200 y el tipo MIME correspondiente.
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'La imagen no existe.'
            ];

            return response()->json($data, $data['code']);
        }
    }

    public function detail($id)
    {
        $user = User::find($id);                                 // Buscamos al usuario en la base de datos por su ID.
    
        // Verificamos si se encontró un usuario con el ID proporcionado.
        if (is_object($user)) {
            $data = [                                            // Si el usuario existe, preparamos una respuesta de éxito con los datos del usuario.
                'status' => 'success', 
                'code' => 200,         
                'user' => $user        
            ];
        } else {
            $data = [                                            // Si el usuario no existe, devolvemos un mensaje de error.
                'status' => 'error',  
                'code' => 400,         
                'message' => 'El usuario no existe.' 
            ];
        }
        
        return response()->json($data, $data['code']);           // Retornamos la respuesta en formato JSON con el código de estado correspondiente.
    }
}
