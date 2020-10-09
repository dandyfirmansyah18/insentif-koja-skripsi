<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionModel extends Model
{
    protected $table = 'tm_position';
    protected $primaryKey = 'POSITION_ID';
    protected $fillable = [
        'POSITION_NAME'
    ];
}
