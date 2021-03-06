<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComidaIngrediente extends Model
{
    use SoftDeletes;
    protected $table = 'comida_ingrediente';

    public function ingrediente(){
        return $this->hasOne('App\Ingredientes','id','ingrediente');
    }
}
