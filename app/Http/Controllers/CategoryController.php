<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;                           //paquete para recoger los datos por solicitud
use App\Models\Category;                               //modelo de la categoria
use Illuminate\Support\Facades\Validator;              //paquete para validar lo que llega 

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();

        $data = [
            'status' => 'success',
            'code' => 200,
            'categories' => $categories
        ];

        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        $json = $request->input('json', null);                               // Obtiene los datos en formato JSON de la solicitud
        $params_array = json_decode($json, true);                            // Decodifica el JSON y lo convierte en un array asociativo

        // Verifica si los datos no están vacíos
        if (!empty($params_array)) {
            $validate = Validator::make($params_array, [                    // Valida los datos recibidos
                'name' => 'required|string|unique:categories,name'
            ]);

            if (!$validate->fails()) {                                       // Si la validación es correcta, crea una nueva instancia de la categoría
                $category = new Category();                                  // Creá la categoria
                $category->name = $params_array['name'];                     // Asigna el nombre de la categoría
                $category->save();                                           // Guarda la categoría en la base de datos

                $data = [                                                    // Respuesta exitosa con los datos de la categoría creada
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Éxito al crear la categoría.',
                    'categorie' => $category
                ];
            } else {                                                          // Si la validación falla, devuelve un mensaje de error
                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error al crear la categoría.'
                ];
            }
        } else {
            $data = [                                                        // Si los datos están vacíos, devuelve un mensaje de error
                'status' => 'error',
                'code' => 404,
                'message' => 'Error al enviar la categoría.'
            ];
        }

        return response()->json($data, $data['code']);                       // Retorna la respuesta en formato JSON con el código de estado correspondiente
    }

    public function update(Request $request, string $id)
    {
        $json = $request->input('json', null);                                 // Recibimos los datos en formato JSON desde la petición
        $params_array = json_decode($json, true);                              // Convertimos el JSON en un array asociativo

        if (!empty($params_array)) {                                           // Verificamos si el array no está vacío
            $validate = Validator::make($params_array, [                       // Validamos los datos recibidos
                'name' => 'required|string|unique:categories,name'
            ]);

            if (!$validate->fails()) {                                          // Si la validación es correcta
                unset($params_array['id']);                                     // Eliminamos los campos que no deben actualizarse
                unset($params_array['created_at']);
                $category = Category::where('id', $id)->first();                // Buscamos la categoría en la base de datos por su ID

                if (!empty($category) && is_object($category)) {                // Si la categoría existe
                    $category->update($params_array);                           // Actualizamos la categoría con los datos proporcionados
                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'changes' => $params_array
                    ];
                } else {                                                         // Si la categoría no existe, enviamos un mensaje de error
                    $data = [
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Error, la categoría no existe.'
                    ];
                }
            } else {                                                               // Si la validación falla, enviamos un mensaje de error
                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error al enviar los datos, la categoría no se ha guardado.'
                ];
            }
        } else {                                                         // Si los datos enviados están vacíos o son inválidos
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Error, no se ha enviado los datos.'
            ];
        }

        return response()->json($data, $data['code']);                     // Devolvemos una respuesta JSON con el código de estado correspondiente
    }

    public function destroy(string $id)
    {
        $category = Category::where('id', $id)->first();                        // Obtenemos la categoría a eliminar buscando por su ID en la base de datos

        if (is_object($category) && !empty($category)) {                        // Verificamos si la categoría existe
            $category->delete();                                                // Eliminamos la categoría de la base de datos

            $data = [                                                            // Preparamos la respuesta indicando que la eliminación fue exitosa
                'status' => 'success', 
                'code' => 200, 
                'categorie' => $category 
            ];
        } else {                                                                 // Si la categoría no existe, enviamos un mensaje de error
            $data = [
                'status' => 'error',
                'code' => 404, 
                'message' => 'Error, la categoría que desea eliminar no existe.'
            ];
        }

        return response()->json($data, $data['code']);                            // Retornamos una respuesta en formato JSON con el código de estado correspondiente
    }
}
