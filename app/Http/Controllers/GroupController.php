<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\GroupModel;
use Response;

class GroupController extends Controller
{
    //
    //
    public function index(Request $request)
    {        
        if($request->get('cari')){
            $datas = GroupModel::where("GROUP_NAME", "LIKE", "%{$request->get('cari')}%")->paginate(10);
        }else{
            $datas = GroupModel::paginate(10);
        }
        return response($datas);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $create = GroupModel::create($input);
        if ($create) {
            return Response::json(array('data' => $create , 'success' => true));
        } else {
            return Response::json(array('data' => $create , 'success' => false));
        }

        // return response($create);
    }

    public function edit($id)
    {
        $data = GroupModel::find($id);
        return response($data);
    }

    public function update(Request $request,$id)
    {
        $input = $request->all();
        $update = GroupModel::where("GROUP_ID",$id)->update($input);
        $data = GroupModel::find($id);
        if ($update) {
            return Response::json(array('data' => $data , 'success' => true));
        }else{
            return Response::json(array('data' => $data , 'success' => false));
        }
        // return response($data);
    }

    public function destroy($id)
    {
        return GroupModel::where('GROUP_ID',$id)->delete();
    }
}
