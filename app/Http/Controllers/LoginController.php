<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\User;
use App\UserModel;
use Response;
use Session;

class LoginController extends Controller
{
    public function doLogin(){

        // validate the info, create rules for the inputs
        $rules = array(
            'email'    => 'required|email', // make sure the email is an actual email
            // 'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
            'password' => 'required|min:3' // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);
        
        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/login')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')) // send back the input (not the password) so that we can repopulate the form
                ->with('error','Format Email Harus Benar dan Password Harus Berupa Angka');
        } else {

            // create our user data for the authentication
            $userdata = array(
                'email'     => Input::get('email'),
                'password'  => Input::get('password')
            );

            $email = Input::get('email');
            $ListUser = UserModel::select('users.*','tr_privileges.PRIVILEGES_NAME','tr_user_status.USER_STATUS_NAME')
                                    ->leftjoin('tr_privileges','tr_privileges.PRIVILEGES_ID','=','users.user_priv')
                                    ->leftjoin('tr_user_status','tr_user_status.USER_STATUS_ID','=','users.user_status')
                                    // ->where('users.email','=',$email)
                                    ->where(DB::raw('BINARY users.email'),$email)
                                    ->first();


            if ($ListUser === null) {
                return Redirect::to('/login')->with('error','Akun Anda Tidak Terdaftar !');
            } else {
                if ($ListUser->user_status == 2) {
                    return Redirect::to('/login')->with('error','Akun Anda Tidak Aktif !');
                }else{
                    if (Auth::attempt($userdata)) {
                        Session::put('login',true);
                        Session::put('email',Input::get('email'));

                        $name = $ListUser->name;
                        $username = $ListUser->username;
                        $priv = $ListUser->PRIVILEGES_NAME;
                        $priv_id = $ListUser->user_priv;

                        // dd($priv_id);

                        Session::put('name',$name);
                        Session::put('username',$username);
                        Session::put('priv',$priv);
                        Session::put('priv_id',$priv_id);

                        return Redirect::to('/');
                    } else {        
                        // validation not successful, send back to form                 
                        return Redirect::to('/login')->with('error','Email Atau Password Anda Salah !');

                    }
                }
            }
            
            

        }
    }

    public function doLogout()
    {
        Auth::logout(); // log the user out of our application
        Session::flush();
        return Redirect::to('/login'); // redirect the user to the login screen
    }
}
