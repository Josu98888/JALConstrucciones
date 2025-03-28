<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //conexión con la tabla services
    protected $table = 'services';

    // campos que se pueden asignar
    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    // ORM
    // Relación de uno a uno (1 servicio pertenece a 1 categoria)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación de uno a muchos (1 servicio tiene N imagenes)
    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
