<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;                                  //paquete para recoger los datos por solicitud
use Illuminate\Support\Facades\Validator;                     //paquete para validar lo que llega
use App\Models\Service;                                       //modelo del servicio

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $json = $request->input('json', null);                                                // Recogemos los datos del formulario que nos llega en formato JSON
        $params_array = json_decode($json, true);                                             // Creamos un array del JSON decodificado

        // Verificamos si los datos decodificados no están vacíos
        if (!empty($params_array)) {
            $params_array = array_map('trim', $params_array);                                  // Eliminamos espacios en blanco de los valores del array

            $validate = Validator::make($params_array, [                                       // Validamos los campos que nos llegan en la solicitud
                'category_id' => 'required',
                'name' => 'required|unique:services',
                'description' => 'required',
            ]);

            // Si la validación es exitosa (no hay errores)
            if (!$validate->fails()) {
                $service = new Service();                                                       // Crear una nueva instancia del modelo 'Service'
                $service->category_id = $params_array['category_id'];                           // Asignamos el ID de la categoría
                $service->name = $params_array['name'];                                         // Asignamos el nombre del servicio
                $service->description = $params_array['description'];                           // Asignamos la descripción del servicio
                $service->save();                                                               // Guardamos el nuevo servicio en la base de datos

                $data = [                                                                       // Preparamos la respuesta con estado de éxito
                    'status' => 'success',
                    'code' => 200,
                    'product' => $service
                ];
            } else {
                $data = [                                                                        // Si la validación falla, devolvemos un mensaje de error
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error al enviar los datos, no se pudo crear el producto.'
                ];
            }
        } else {
            $data = [                                                                             // Si los datos recibidos están vacíos o no son válidos
                'status' => 'error',
                'code' => 404,
                'message' => 'Error, no se han enviados los datos.',
                'params_array' => $params_array
            ];
        }

        return response()->json($data, $data['code']);                                            // Retornamos la respuesta en formato JSON con el código de estado correspondiente
    }

    public function update(Request $request, string $id)
    {
        $json = $request->input('json', null);                                                       // Obtenemos el JSON enviado en la petición
        $params_array = json_decode($json, true);                                                    // Decodificamos el JSON a un array asociativo

        if (!empty($params_array)) {                                                                 // Verificamos que los datos no estén vacíos
            $validate = Validator::make($params_array, [                                             // Validamos los datos enviados en el request
                'category_id' => 'required',              
                'name' => 'required|unique:services,name,'. $id,     
                'description' => 'required',              
            ]);

            if (!$validate->fails()) {                                                                // Si la validación es correcta (no hay errores)
                $service = Service::find($id);                                                        // Buscamos el servicio en la base de datos por su ID
                if (!is_null($service)) {                                                             // Verificamos si el servicio existe
                    $service->category_id = $params_array['category_id'];                             // Asignamos el ID de la categoría
                    $service->name = $params_array['name'];                                           // Asignamos el nombre del servicio
                    $service->description = $params_array['description'];                             // Asignamos la descripción del servicio
                    $service->save();                                                                 // Guardamos los cambios en la base de datos

                    $data = [                                                                         // Retornamos una respuesta exitosa con los cambios realizados
                        'status' => 'success',
                        'code' => 200,
                        'changes' => $service 
                    ];
                } else {
                    $data = [                                                                          // Si el servicio no existe, enviamos un mensaje de error
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Error, el servicio no existe.'
                    ];
                }
            } else {
                $data = [                                                                               // Si los datos enviados no pasan la validación, enviamos un mensaje de error
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error, los datos enviados son incorrectos.',
                    'errors' => $validate->errors()
                ];
            }
        } else {
            $data = [                                                                                   // Si no se enviaron datos en la petición, enviamos un mensaje de error
                'status' => 'error',
                'code' => 404,
                'message' => 'Error, no se han enviado los datos'
            ];
        }

        return response()->json($data, $data['code']);                                                  // Retornamos la respuesta en formato JSON con el código de estado correspondiente
    }
}
