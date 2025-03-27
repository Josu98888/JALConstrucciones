<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;                           //paquete para recoger los datos por solicitud
use App\Models\Category;                               //modelo de la categoria
use Illuminate\Support\Facades\Validator;              //paquete para validar lo que llega 

class CategoryController extends Controller
{
    public function index() {}

    public function store(Request $request)
    {
        $json = $request->input('json', null);                               // Obtiene los datos en formato JSON de la solicitud
        $params_array = json_decode($json, true);                            // Decodifica el JSON y lo convierte en un array asociativo

        // Verifica si los datos no están vacíos
        if (!empty($params_array)) {
            $validate = Validator::make($params_array, [                    // Valida los datos recibidos
                'name' => 'required|string' 
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
