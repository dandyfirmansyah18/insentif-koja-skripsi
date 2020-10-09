<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempInsentifModel extends Model
{
    protected $table = 'tx_temp_incentive';
    public $timestamps = false;
    protected $primaryKey = 'TEMP_INCENTIVE_ID';
    protected $fillable = [
        'INCENTIVE_PARAM_ID','EMPLOYEE_NIK','TEMP_INCENTIVE_PERCENT_CUT','TEMP_INCENTIVE_COST_CUT','TEMP_INCENTIVE_TOTAL_AFTER_CUT','TEMP_INCENTIVE_KPI','TEMP_INCENTIVE_FLAT','TEMP_INCENTIVE_BOX','TEMP_INCENTIVE_OJEKER','TEMP_INCENTIVE_THRESHOLD','TEMP_INCENTIVE_TOTAL','TEMP_INCENTIVE_DIST_CUT'
    ];
}
