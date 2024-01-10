<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerAuthController extends Controller
{
    function __construct() {
        Config::set('jwt.user', Customer::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ]]);
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

    public function updateProfile(Request $request){
        $this->validate($request,[
            'first_name' => 'required|string',
            'email' => 'required',
        ]);

        $customer = Customer::where('id',$request->id)->first();
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->ages_of_children = $request->ages_of_children;
        $customer->ages_of_father = $request->ages_of_father;
        $customer->customer_status = 'Y';
        $customer->save();

        return response()->json([
            'status'=>200,
            'message'=>'success'
        ],200);
    }

    public function changePassword(Request $request){
        $this->validate($request,[
            'previous_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $current_password = Auth::User()->Password;
        $customer = JWTAuth::parseToken()->authenticate();

        if(Hash::check($request->previous_password, $current_password))
        {
            if(Hash::check($request->password, $current_password)){
                return response()->json(['message'=>'Previous Password and Old Password Same']);
            }else{
                $customer = Customer::where('ID',$customer->ID)->first();
                $customer->Password = bcrypt($request->password);
                $customer->save();
                return response()->json(['message'=>'Password Change successfully :)']);
            }

        }else{
            return response()->json(['message'=>'Previous Password Not Correct :)']);
        }
    }

    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json($user);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        try {
            $this->guard()->logout();
        } catch (\Exception $exception) {

        }
        return response()->json([
            'status'=>200,
            'message' => 'Successfully logged out'
        ],200);
    }

    public function sendsms($ip, $userid, $password, $smstext, $receipient) {
        $smsUrl = "http://{$ip}/httpapi/sendsms?userId={$userid}&password={$password}&smsText=" . $smstext . "&commaSeperatedReceiverNumbers=" . $receipient;
        //echo $smsUrl; exit();
        $response = file_get_contents($smsUrl);
        //print_r($response); exit();
        return json_decode($response);
    }
}
