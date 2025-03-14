<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //conexión con la tabla services
    protected $table = 'services';

    // campos que se pueden asignar
    protected $fillable = [
        'name',
        'description',
    ];
}
