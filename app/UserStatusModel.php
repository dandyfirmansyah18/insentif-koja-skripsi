<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStatusModel extends Model
{
    protected $table = 'tr_user_status';
    protected $primaryKey = 'USER_STATUS_ID';
    protected $fillable = [
        'USER_STATUS_NAME'
    ];
}
