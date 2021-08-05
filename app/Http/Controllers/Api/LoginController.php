<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Validator;
use Auth;

class LoginController extends Controller
{
    public function signup(Request $request,$email){
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:20',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 0,
                'message' => "Fill The Required Fields",
                'errors' => $validator->messages()->toArray()
            ], 200);
        } else {
            $check = User::where('email',$email)->first();
            if($check){
                if($check->status == 1 && $check->is_invite == 1){
                    return response()->json([
                        'status' => 0,
                        'message' => "User Already Registred"
                    ], 200);
                }
                $usernameExist = User::where('username',$request->username)->where('id','!=',$check->id)->first();
                if($usernameExist){
                    return response()->json([
                        'status' => 0,
                        'message' => "User Name Already Exist"
                    ], 200);
                }
                $emailExist = User::where('email',$email)->where('id','!=',$check->id)->first();
                if($emailExist){
                    return response()->json([
                        'status' => 0,
                        'message' => "Email Already Exist"
                    ], 200);
                }
                $pin_no = rand(100000,999999);
                $check->pin_no = $pin_no;
                $check->username = $request->username;
                $check->password = bcrypt($request->password);
                $check->save();

                Mail::to($email)->send(new \App\Mail\SendPinMail($pin_no));

                return response()->json([
                        'status' => 1,
                        'message' => "PIN Successfully Sent On Your Email ID"
                    ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => "Invalid Registration Link"
                ], 200);
            }
        }
    }
    public function verifyPin(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:20',
            'pin' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 0,
                'message' => "Fill The Required Fields",
                'errors' => $validator->messages()->toArray()
            ], 200);
        } else {
            $check = User::where('username',$request->username)->first();
            if($check){
                if($check->pin_no == $request->pin){
                    $check->status = 1;
                    $check->is_invite = 1;
                    $check->pin_no = null;
                    $check->registered_at = date("Y-m-d H:i:s");
                    $check->save();
                    return response()->json([
                        'status' => 1,
                        'message' => "Users Registered Successfully, Now You Can Login"
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => "Invalide PIN"
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => "User Not Found With Given Username"
                ], 200);
            }
        }
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:20',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 0,
                'message' => "Fill The Required Fields",
                'errors' => $validator->messages()->toArray()
            ], 200);
        }
        $credentials = $request->only(['username','password']);
        $user = User::select('id','name','username','email','avatar','user_role','status','created_at','registered_at')->where(['username'=>$request->username])->first();
        
        if($user){
            if(Auth::attempt($credentials)){
                if($user->status == 0){
                    return response()->json(['status' =>0,'message'=>'You Are In Active, Not Allowed To Login','data'=>$user]);
                }
                return response()->json([
                    'success' => 1,
                    'message' => 'Login Success',
                    'data' => $user,
                ]);
            } else {
                return response()->json(['status' =>0,'message'=>'Credentials does not match.']);
            }
        }
        return response()->json(['status' => 0,'message'=>'User not found.']);
    }
    public function updateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'username' => 'required|min:4|max:20',
            'avatar' => 'dimensions:min_width=256,min_height=256,max_width=256,max_height=256',
            'email' => 'required|string|email',
            'user_role' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 0,
                'message' => "Fill The Required Fields",
                'errors' => $validator->messages()->toArray()
            ], 200);
        } else {
            $check = User::where('id',$request->user_id)->first();
            if($check){

                $usernameExist = User::where('username',$request->username)->where('id','!=',$check->id)->first();
                if($usernameExist){
                    return response()->json([
                        'status' => 0,
                        'message' => "User Name Already Exist"
                    ], 200);
                }
                $emailExist = User::where('email',$request->email)->where('id','!=',$check->id)->first();
                if($emailExist){
                    return response()->json([
                        'status' => 0,
                        'message' => "Email Already Exist"
                    ], 200);
                }

                if($check->status == 1){
                    $check->name = $request->name;
                    $check->username = $request->username;
                    $check->email = $request->email;
                    $check->user_role = $request->user_role;
                    $path = 'uploads/avatar/';
                    /*if (!file_exists($path)){
                        mkdir($path, 0755, true);
                    }*/
                    if ($request->hasFile('avatar')) {
                        $file    = $request->file('avatar');
                        $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
                        if (!file_exists($path)) {
                            \File::makeDirectory($path, $mode = 0777, true, true);
                        }
                        $file->move($path, $filename);
                        if($check->avatar && file_exists($path.$check->avatar)){
                            unlink($path.$check->avatar);
                        }
                        $check->avatar = $filename;
                    }
                    $check->save();
                    $newdata = User::select('id','name','username','email','avatar','user_role','status','created_at','registered_at')->where(['id'=>$check->id])->first();
                    return response()->json([
                        'status' => 1,
                        'message' => "Detail Updated Successfully",
                        'data'=>$newdata
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => "You Are Inactive, Not Allowed To Update Profile"
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => "User Not Found"
                ], 200);
            }
        }
    }
}
