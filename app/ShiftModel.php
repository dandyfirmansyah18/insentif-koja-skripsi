<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShiftModel extends Model
{
    protected $table = 'tr_shift';
    protected $primaryKey = 'SHIFT_ID';
    protected $fillable = [
        'SHIFT_NAME'
    ];
}
