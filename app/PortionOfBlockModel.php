<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortionOfBlockModel extends Model
{
    protected $table = 'tx_portion_of_block';
    // public $timestamps = false;
    protected $primaryKey = 'POB_ID';
    protected $fillable = [
        'INCENTIVE_PARAM_ID','BLOCK_ID','POB_DIFF_PER_BLOCK','HEADACOUNT_PER_BLOCK','POB_COEFFICIENT_UPPER_VALUE','POB_BOBOT','POB_PERSENTASE_BLOCK','POB_PERSENTASE_BLOCK_PER_EMP',
        'POB_KUE_PER_BLOCK','POB_AVG_PER_EMP','POB_DIFF_NOMINAL','POB_PERSENTASE_DIFF_NOMINAL','POB_BLOCK_COMPOSITION'
    ];
}
