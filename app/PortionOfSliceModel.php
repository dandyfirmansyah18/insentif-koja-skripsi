<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortionOfSliceModel extends Model
{
    protected $table = 'tx_portion_of_slice';
    // public $timestamps = false;
    protected $primaryKey = 'POS_ID';
    protected $fillable = [
        'INCENTIVE_PARAM_ID','BLOCK_ID','SLICE_ID','POS_DIFF_PER_SLICE','POS_HEADACOUNT_PER_SLICE','POS_COEFFICIENT_UPPER_VALUE','POS_BOBOT','POS_PERSENTASE_SLICE',
        'POS_PERSENTASE_SLICE_PER_EMP','POS_KUE_PER_SLICE','POS_NOM_FLAT_DIST_BLOCK_I'
    ];
}
