<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\UserModel;
use App\PrivilegeModel;
use App\UserStatusModel;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function index(Request $request)
    {
        if($request->get('cari')){
            $datas = UserModel::select('users.*','tr_privileges.PRIVILEGES_NAME','tr_user_status.USER_STATUS_NAME')
                                ->leftjoin('tr_privileges','tr_privileges.PRIVILEGES_ID','=','users.user_priv')
                                ->leftjoin('tr_user_status','tr_user_status.USER_STATUS_ID','=','users.user_status')
                                ->where("users.name", "LIKE", "%{$request->get('cari')}%")
                                ->orWhere("users.username", "LIKE", "%{$request->get('cari')}%")
                                ->orWhere("users.email", "LIKE", "%{$request->get('cari')}%")
                                ->orWhere("tr_privileges.PRIVILEGES_NAME", "LIKE", "%{$request->get('cari')}%")
                                ->paginate(10);
        }else{
            $datas = UserModel::select('users.*','tr_privileges.PRIVILEGES_NAME','tr_user_status.USER_STATUS_NAME')
                                ->leftjoin('tr_privileges','tr_privileges.PRIVILEGES_ID','=','users.user_priv')
                                ->leftjoin('tr_user_status','tr_user_status.USER_STATUS_ID','=','users.user_status')
                                ->paginate(10);
        }
        return response($datas);
    }

    public function store(Request $request)
    {
        // $password = $this->generateRandomString();
        // $password = 'K970345';
        // $password = 'P@ssw0rd123';
        $password = $request->get('password');
        $passwordfix = Hash::make($password);

        $name = $request->get('name');
        $username = $request->get('username');
        $email = $request->get('email');
        $privillege = $request->get('user_priv');
        $status = $request->get('user_status');

        $create = new UserModel();
        $create->name = $name;
        $create->username = $username;
        $create->email = $email;
        $create->user_priv = $privillege;
        $create->user_status = $status;
        $create->password = $passwordfix;

        $create->save();

        $full = UserModel::select('users.*','tr_privileges.PRIVILEGES_NAME','tr_user_status.USER_STATUS_NAME')
                                ->leftjoin('tr_privileges','tr_privileges.PRIVILEGES_ID','=','users.user_priv')
                                ->leftjoin('tr_user_status','tr_user_status.USER_STATUS_ID','=','users.user_status')
                                ->where("users.id", $create->id)->first();

        if ($create) {
            return Response::json(array('data' => $full , 'success' => true));
        } else {
            return Response::json(array('data' => $full , 'success' => false));
        }                                
    }

    public function edit($id)
    {
        $data = UserModel::find($id);
        return response($data);
    }

    public function update(Request $request,$id)
    {
        $input = $request->all();
        $update = UserModel::where("id",$id)->update($input);
        $data = UserModel::select('users.*','tr_privileges.PRIVILEGES_NAME','tr_user_status.USER_STATUS_NAME')
                                ->leftjoin('tr_privileges','tr_privileges.PRIVILEGES_ID','=','users.user_priv')
                                ->leftjoin('tr_user_status','tr_user_status.USER_STATUS_ID','=','users.user_status')->find($id);
        if ($update) {
            return Response::json(array('data' => $data , 'success' => true));
        }else{
            return Response::json(array('data' => $data , 'success' => false));
        }
    }

    public function resetPassword(Request $request){
        $update = UserModel::where('id',$request->get('id_user'))->first();
        $update->password = Hash::make($request->get('password_user'));
        if ($update->save()) {
            return Response::json(array('message' => 'Reset Password Success.' , 'success' => true));
        }else{
            return Response::json(array('message' => 'Invalid Reset Password.' , 'success' => false));
        }
    }

    public function destroy($id)
    {
        return UserModel::where('id',$id)->delete();
    }

    public function getPrivileges(){
        $data = PrivilegeModel::select('*')->get();
        return response($data);
    }

    public function getUserStatus(){
        $data = UserStatusModel::select('*')->get();
        return response($data);
    }
}
