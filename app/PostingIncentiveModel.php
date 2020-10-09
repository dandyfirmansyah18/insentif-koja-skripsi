<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostingIncentiveModel extends Model
{
    protected $table = 'tx_posting_incentive';
    // public $timestamps = false;
    protected $primaryKey = 'POSTING_INCENTIVE_ID';
    protected $fillable = [
        'INCENTIVE_PARAM_ID','POSTING_INCENTIVE_NIK','POSTING_INCENTIVE_NAME','POSTING_INCENTIVE_POS','POSTING_INCENTIVE_GROUP','POSTING_INCENTIVE_SHIFT','POSTING_INCENTIVE_BLOCK'
        ,'POSTING_INCENTIVE_SLICE','POSTING_INCENTIVE_DIV','POSTING_INCENTIVE_GRADE','POSTING_INCENTIVE_POB_DIFF_BLOCK_I','POSTING_INCENTIVE_POB_DIFF_BLOCK_II'
        ,'POSTING_INCENTIVE_POB_DIFF_BLOCK_III','POSTING_INCENTIVE_POB_CUV_BLOCK_I','POSTING_INCENTIVE_DIST_FLAT_BLOCK_I','POSTING_INCENTIVE_DIFF_SLICE_BLOCK_I'
        ,'POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIA','POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIA','POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIA','POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIA'
        ,'POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIA','POSTING_INCENTIVE_DIFF_SLICE_BLOCK_IIB','POSTING_INCENTIVE_DIFF_SLICE_BLOCK_III','POSTING_INCENTIVE_POS_CUV_BLOCK_I'
        ,'POSTING_INCENTIVE_POS_CUV_BLOCK_II','POSTING_INCENTIVE_POS_CUV_BLOCK_III','POSTING_INCENTIVE_FLAT','POSTING_INCENTIVE_BOX','POSTING_INCENTIVE_OJEKER'
        ,'POSTING_INCENTIVE_THRESHOLD','POSTING_INCENTIVE_FINAL','POSTING_INCENTIVE_BA_MIN_BOX_QCC','POSTING_INCENTIVE_BA_MIN_BOX_RTG'
        ,'POSTING_INCENTIVE_PERCENT_CUT','POSTING_INCENTIVE_COST_CUT','POSTING_INCENTIVE_FINAL_AFTER_CUT','POSTING_INCENTIVE_DIST_CUT'
    ];
}
