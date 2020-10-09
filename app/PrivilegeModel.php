<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivilegeModel extends Model
{
    protected $table = 'tr_privileges';
    protected $primaryKey = 'PRIVILEGES_ID';
    protected $fillable = [
        'PRIVILEGES_NAME'
    ];
}
