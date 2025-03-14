<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //conexión con la tabla images 
    protected $table = 'images';

    // campos que se puden asignar
    protected $fillable = [
        'url',
    ];
}
