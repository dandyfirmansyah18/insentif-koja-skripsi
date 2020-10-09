<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SliceModel extends Model
{
    protected $table = 'tr_slice';
    protected $primaryKey = 'SLICE_ID';
    protected $fillable = [
        'SLICE_NAME','SLICE_BLOCK','SLICE_SHIFT','GRADE'
    ];
}
