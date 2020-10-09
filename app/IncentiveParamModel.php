<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncentiveParamModel extends Model
{
    protected $table = 'tx_incentive_param';
    public $timestamps = false;
    protected $primaryKey = 'INCENTIVE_PARAM_ID';
    protected $fillable = [
        'INCENTIVE_PARAM_MONTH','INCENTIVE_PARAM_YEAR','INCENTIVE_PARAM_INCOME','INCENTIVE_PARAM_VALUTA','INCENTIVE_PARAM_DIST','INCENTIVE_PARAM_CALCULATE_AGAIN','INCENTIVE_PARAM_VALUE'
    ];
}
