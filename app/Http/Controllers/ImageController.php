<?php

namespace App\Http\Controllers;

use App\Models\Image;                                                                          // Importa el modelo Image
use Illuminate\Http\Request;                                                                   // Ipaque para manejo de solicitudes HTTP
use Illuminate\Support\Facades\File;                                                           // paquete para manejo de archivos
use Illuminate\Support\Facades\Storage;                                                        // paquete para almacenamiento de archivos
use Illuminate\Support\Facades\Validator;                                                      // paquete para validaciones

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $json = $request->input('json', null);                                                  // Obtiene el JSON enviado en la solicitud
        $params_array = json_decode($json, true);                                               // Decodifica el JSON a un array asociativo

        $validate = Validator::make($params_array, [                                            // Valida que el campo 'service_id' esté presente
            'service_id' => 'required',
        ]);

        if (!$validate->fails()) {                                                               // Verifica si la validación ha fallado
            $imageCount = Image::where('service_id', $params_array['service_id'])->count();      // Cuenta las imágenes asociadas al servicio
            $maxImages = 3;                                                                      // Número máximo de imágenes por servicio
        
            if ($imageCount < $maxImages) {                                                      // Verifica que no se hayan subido más de 5 imágenes
                $image = new Image();                                                            // Crea una nueva instancia del modelo Image            
                $image->service_id = $params_array['service_id'];                                // Asigna el ID del servicio a la imagen

                if ($request->hasFile('image')) {                                                // Verifica si se ha enviado un archivo de imagen    
                    $imageFile = $request->file('image');                                        // Obtiene el archivo de imagen    
                    $image_name = time() . '_' . $imageFile->getClientOriginalName();            // Genera un nombre único para la imagen usando el tiempo actual                
                    Storage::disk('images')->put($image_name, File::get($imageFile));            // Guarda la imagen en el disco 'images'    
                    $image->url = $image_name;                                                   // Guarda el nombre de la imagen en la base de datos
                }
                $image->save();                                                                  // Guarda la imagen en la base de datos

                $data = [                                                                        // Respuesta en caso de éxito
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Éxito, imagen subida correctamente.',
                    'image' => $image
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Error, no se pueden subir más de 3 imágenes por servicio.'
                ];
            }
        } else {
            $data = [                                                                     // Respuesta en caso de error en la validación
                'status' => 'error',
                'code' => 400,
                'message' => 'Error al subir la imagen.',
                'errors' => $validate->errors()                                           // Devuelve los errores de validación
            ];
        }

        return response()->json($data, $data['code']);                                     // Retorna la respuesta en formato JSON con el código de estado correspondient
    }
}
