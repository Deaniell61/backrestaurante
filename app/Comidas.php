<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comidas extends Model
{
    use SoftDeletes;
    protected $table = 'comidas';
}
