<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\UserLog;
use App\Services\AccessLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    function __construct() {
        Config::set('jwt.user', Customer::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ]]);
    }

    public function changePass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPass' => 'required',
            'newPass' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        $authUser = JWTAuth::parseToken()->authenticate();

        $check = Hash::check($request->oldPass, $authUser->Password);
        if ($check) {
            $user = User::find($authUser->UserID);
            $user->Password = bcrypt($request->newPass);
            $user->save();
            return response()->json(['message' => "Password changed successfully"]);
        } else {
            return response()->json(['message' => "Your current password is Invalid"], 400);
        }
    }

    public function changeProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'image' => 'required',
            'isImageChange' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        $authUser = JWTAuth::parseToken()->authenticate();
        $user = User::find($authUser->UserID);
        $user->UserName = $request->name;
        $user->Email = $request->email;
        $user->Phone = $request->mobile;
        if ($request->isImageChange) {
            if ($authUser->Avatar !== 'default.png') {
                try {
                    unlink(public_path('uploads/') . $authUser->Avatar);
                } catch (\Exception $e) {
                }
            }
            $image = $request->image;
            $name = rand(0, 10000000) . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($image)->save(public_path('uploads/') . $name);
            $user->Avatar = $name;
        }
        $user->save();
        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        if ($token = JWTAuth::attempt(['email' => $request->email,'password' => $request->password])) {
            return response()->json([
                'status'=>200,
                'token'=>$token,
            ],200);
        } else {
            return response()->json([
                'message'=>'UserId or Password Not Match',
                'status'=>401
            ],200);
        }
    }

    public function registration(Request $request)
    {
        $this->validate($request,[
            'first_name' => 'required|string',
            'email' => 'required',
            'password' => 'required|string|min:6'
        ]);

        try {
            $want_to_receive_email = $request->want_to_receive_email;
            if ($want_to_receive_email == true){
                $status = 'Y';
            }else{
                $status = 'N';
            }
            $customer = new Customer();
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->ages_of_children = $request->ages_of_children;
            $customer->ages_of_father = $request->ages_of_father;
            $customer->want_to_receive_email = $status;
            $customer->customer_status = 'Y';
            $customer->password = bcrypt($request->password);
            $customer->save();
            return response()->json(['message' => "success"]);

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function me()
    {
        return response()->json($this->guard()->user());

    }

    public function logout()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            //UserLog::create(['UserId' => $user->ID, 'TransactionTime' => Carbon::now(), 'TransactionDetails' => "Logged Out"]);
            $this->guard()->logout();
        } catch (\Exception $exception) {

        }
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard('api');
    }
}
