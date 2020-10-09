<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\AdjustParamModel;

class AdjustParamController extends Controller
{
    
    public function getAdjustParam(){
        $data = AdjustParamModel::select('*')->first();
        return response($data);
    }

    public function saveEdit(Request $request)
    {
        $id = 1;
        $input = $request->all();
        AdjustParamModel::where("ADJUST_PARAM_ID",$id)->update($input);
        $data = AdjustParamModel::find($id);
        
        return Response::json(array('success'=>true));
    }

}
