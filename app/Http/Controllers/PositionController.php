<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\PositionModel;
use Response;

class PositionController extends Controller
{
    //
    //
    public function index(Request $request)
    {
        if($request->get('cari')){
            $datas = PositionModel::where("POSITION_NAME", "LIKE", "%{$request->get('cari')}%")->paginate(10);
        }else{
            $datas = PositionModel::paginate(10);
        }
        return response($datas);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $create = PositionModel::create($input);
        if ($create) {
            return Response::json(array('data' => $create , 'success' => true));
        } else {
            return Response::json(array('data' => $create , 'success' => false));
        }
        
    }

    public function edit($id)
    {
        $data = PositionModel::find($id);
        return response($data);
    }

    public function update(Request $request,$id)
    {
        $input = $request->all();
        $update = PositionModel::where("POSITION_ID",$id)->update($input);
        $data = PositionModel::find($id);
        if ($update) {
            return Response::json(array('data' => $data , 'success' => true));
        }else{
            return Response::json(array('data' => $data , 'success' => false));
        }
        // return response($data);
    }

    public function destroy($id)
    {
        return PositionModel::where('POSITION_ID',$id)->delete();
    }
}
