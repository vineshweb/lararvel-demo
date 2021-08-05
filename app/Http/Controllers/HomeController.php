<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Validator;
use Auth;

class HomeController extends Controller
{
    public function index(){
        return view('home');
    }
    public function addInvite(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        if($validator->fails()){
            $message = "Fill The Required Fields";
            \Session::flash('error', $message);
            return redirect()->back()->withErrors($validator->messages()->toArray())->withInput();
        } else {
            $email = $request->email;
            $check = User::where('email',$email)->first();
            if(!$check){
                $create = ['email'=>$email];
                User::create($create);

                Mail::to($email)->send(new \App\Mail\SendPinMail($email,1));

                $message = "User Invite Successfully";
                \Session::flash('success', $message);
                return redirect()->back()->withInput();
            } else {
                $message = "User Already Registred";
                \Session::flash('error', $message);
                return redirect()->back()->withInput();
            }
        }
    }
}
