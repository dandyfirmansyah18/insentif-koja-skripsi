<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'username', 'email', 'password', 'remember_token', 'user_status','user_priv'
    ];
    // protected $timeStamps
}
