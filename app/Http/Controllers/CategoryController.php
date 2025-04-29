<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;                           //paquete para recoger los datos por solicitud
use App\Models\Category;                               //modelo de la categoria
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
            $validate = Validator::make($request->all(), [                                         // Valida los datos recibidos
                'name' => 'required|string|unique:categories,name',
                'description' => 'required|string',
                'image' => 'image|mimes:jpg,png,jpeg,gif'
            ]);

            if (!$validate->fails()) {                                                           // Si la validación es correcta, crea una nueva instancia de la categoría
                $quantityCategories = Category::count();                                         // Cuenta la cantidad de categorías en la base de datos
                $limitCategories = 8;                                                            // Límite de categorías permitidas

                if ($quantityCategories <= $limitCategories) {                                   // Verifica si se ha alcanzado el límite de categorías
                    $category = new Category();                                                  // Creá la categoria
                    $category->name =$request->input('name');;                                     // Asigna el nombre de la categoría
                    $category->description = $request->input('description');;                       // Asigna la descripción de la categoría

                    if ($request->hasFile('image')) {                                            // Verifica si se ha enviado una imagen
                        $image = $request->file('image');                                        // Obtiene la imagen                   
                        $image_name = time() . '_' . $image->getClientOriginalName();            // Asigna un nombre único
                        Storage::disk('categories')->put($image_name, File::get($image));        // Guarda nueva imagen
                        $category->image = $image_name;                                          // Guardar la ruta relativa en la base de datos
                    }
                    $category->save();                                                           // Guarda la categoría en la base de datos

                    $data = [                                                                    // Respuesta exitosa con los datos de la categoría creada
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Éxito al crear la categoría.',
                        'categorie' => $category
                    ];
                } else {
                    $data = [                                                                    // Respuesta en caso de haber alcanzado el límite de categorías 
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Error, no se pueden crear más de 8 categorias.'
                    ];
                }
            } else {                                                                             // Si la validación falla, devuelve un mensaje de error
                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Error al crear la categoría.',
                    'errors' => $validate->errors()
                ];
            }
        

        return response()->json($data, $data['code']);                                            // Retorna la respuesta en formato JSON con el código de estado correspondiente
    }

    public function update(Request $request, string $id)
    {
        $json = $request->input('json', null);                                 // Recibimos los datos en formato JSON desde la petición
        $params_array = json_decode($json, true);                              // Convertimos el JSON en un array asociativo

        if (!empty($params_array)) {                                           // Verificamos si el array no está vacío
            $validate = Validator::make($params_array, [                       // Validamos los datos recibidos
                'name' => 'required|string|unique:categories,name,' . $id,
                'description' => 'required|string',
                'image' => 'image|mimes:jpg,png,jpeg,gif'
            ]);

            if (!$validate->fails()) {                                          // Si la validación es correcta
                unset($params_array['id']);                                     // Eliminamos los campos que no deben actualizarse
                unset($params_array['created_at']);
                $category = Category::where('id', $id)->first();                // Buscamos la categoría en la base de datos por su ID

                if (!empty($category) && is_object($category)) {                // Si la categoría existe
                    
                    if ($request->hasFile('image')) {

                        if ($category->image) {
                            Storage::disk('categories')->delete($category->image);               // Elimina imagen anterior si existe
                        }
                        $image = $request->file('image');
                        $image_name = time() . '_' . $image->getClientOriginalName();   // Asigna un nombre único
                        Storage::disk('categories')->put($image_name, File::get($image));    // Guarda nueva imagen
                        $category->image = $image_name;                                     // Guardar la ruta relativa en la base de datos
                    }
                    $category->update($params_array);                           // Actualizamos la categoría con los datos proporcionados
                    $category->save();                                          // Guardamos los cambios en la base de datos

                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'changes' => $category
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

    public function getImage($filename)
    {
        $path = storage_path("app/public/categories/{$filename}");                    // Construye la ruta completa donde se encuentra la imagen en el almacenamiento.

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
