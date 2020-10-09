<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DivisiModel extends Model
{
    protected $table = 'tm_division';
    protected $primaryKey = 'DIVISION_ID';
    protected $fillable = [
        'DIVISION_NAME'
    ];
}
