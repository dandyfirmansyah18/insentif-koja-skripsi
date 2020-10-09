<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeModel extends Model
{
    //
    public $incrementing = false;
    protected $table = 'tm_employee';
    protected $primaryKey = 'EMPLOYEE_NIK';
    protected $fillable = [
        'EMPLOYEE_NIK', 'EMPLOYEE_NAME', 'EMPLOYEE_POS', 'EMPLOYEE_GROUP', 'EMPLOYEE_SHIFT', 'EMPLOYEE_BLOCK', 'EMPLOYEE_SLICE', 'EMPLOYEE_DIV', 
        'EMPLOYEE_GRADE','EMPLOYEE_STATUS'
    ];
}