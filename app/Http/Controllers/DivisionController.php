<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\DivisiModel;
use Response;

class DivisionController extends Controller
{
    //
    //
    public function index(Request $request)
    {
        if($request->get('cari')){
            $datas = DivisiModel::where("DIVISION_NAME", "LIKE", "%{$request->get('cari')}%")->paginate(10);
        }else{
            $datas = DivisiModel::paginate(10);
        }
        return response($datas);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $create = DivisiModel::create($input);
        if ($create) {
            return Response::json(array('data' => $create , 'success' => true));
        } else {
            return Response::json(array('data' => $create , 'success' => false));
        }
    }

    public function edit($id)
    {
        $data = DivisiModel::find($id);
        return response($data);
    }

    public function update(Request $request,$id)
    {
        $input = $request->all();
        $update = DivisiModel::where("DIVISION_ID",$id)->update($input);
        $data = DivisiModel::find($id);
        if ($update) {
            return Response::json(array('data' => $data , 'success' => true));
        }else{
            return Response::json(array('data' => $data , 'success' => false));
        }
        
    }

    public function destroy($id)
    {
        return DivisiModel::where('DIVISION_ID',$id)->delete();
    }
}
