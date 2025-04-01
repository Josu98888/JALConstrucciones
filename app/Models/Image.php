<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //conexión con la tabla images 
    protected $table = 'images';

    // campos que se puden asignar
    protected $fillable = [
        'service_id',
        'url',
    ];

    // ORM 
    // Relación de uno a muchos inversa (N imagenes pertenecen a 1 servicio)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
