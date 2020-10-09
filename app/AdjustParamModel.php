<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdjustParamModel extends Model
{
    protected $table = 'tr_adjust_param';
    public $timestamps = false;
    protected $primaryKey = 'ADJUST_PARAM_ID';
    protected $fillable = [
        'AP_PERCENTASE_INCENTIVE','AP_POB_DIFF_BLOCK_I','AP_POB_DIFF_BLOCK_II','AP_POB_DIFF_BLOCK_III','AP_POB_CUV_BLOCK_I','AP_POS_DIST_FLAT_BLOCK_I','AP_POS_DIFF_SLICE_BLOCK_I','AP_POS_DIFF_SLICE_BLOCK_IIA',
        'AP_POS_DIFF_SLICE_BLOCK_IIB','AP_POS_DIFF_SLICE_BLOCK_III','AP_POS_CUV_BLOCK_I','AP_POS_CUV_BLOCK_II','AP_POS_CUV_BLOCK_III','AP_PENALTY','AP_BA_MIN_BOX_QCC','AP_BA_MIN_BOX_RTG','AP_COST_BOX_QCC','AP_COST_BOX_RTG'
    ];
}
