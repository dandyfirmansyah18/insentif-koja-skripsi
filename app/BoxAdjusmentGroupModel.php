<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoxAdjusmentGroupModel extends Model
{
    protected $table = 'tx_box_adjusment_group';
    // public $timestamps = false;
    protected $primaryKey = 'BOX_ADJUSMENT_GROUP_ID';
    protected $fillable = [
        'INCENTIVE_PARAM_ID','GROUP_ID','BOX_ADJUSMENT_GROUP_VALUE','BOX_ADJUSMENT_GROUP_INCENTIVE','BOX_ADJUSMENT_GROUP_OVER_BOX','BOX_ADJUSMENT_GROUP_PRICE'
    ];
}
