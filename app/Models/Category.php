<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //conexión con la tabla categories 
    protected $table = 'categories';

    // campos que se pueden asignar
    protected $fillable = [
        'name'
    ];
}
