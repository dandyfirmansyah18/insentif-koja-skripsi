<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockModel extends Model
{
    protected $table = 'tr_block';
    protected $primaryKey = 'BLOCK_ID';
    protected $fillable = [
        'BLOCK_NAME'
    ];
}
