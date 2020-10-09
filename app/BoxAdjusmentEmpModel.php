<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoxAdjusmentEmpModel extends Model
{
    protected $table = 'tx_box_adjusment_emp';
    // public $timestamps = false;
    protected $primaryKey = 'BA_ID';
    protected $fillable = [
        'INCENTIVE_PARAM_ID','BOX_ADJUSMENT_GROUP_ID','EMPLOYEE_NIK','BAE_BOX_HANDLE','BAE_INCENTIVE_EMP','BAE_OJEKER_BOX',
        'BAE_OJEKER_BOX_PRICE','BAE_OJEKER_TOTAL','BAE_RE_DISTRIBUTE','BAE_INCENTIVE_EMP_FINAL'
    ];
}
