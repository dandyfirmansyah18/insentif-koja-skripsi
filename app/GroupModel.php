<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupModel extends Model
{
    protected $table = 'tm_group';
    protected $primaryKey = 'GROUP_ID';
    protected $fillable = [
        'GROUP_NAME'
    ];
}
