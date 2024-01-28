<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

class ActivityController extends Controller
{
    public function joinEvent(Request $request){
        $this->validate($request,[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'ages_of_children'=>'required',
            'ages_of_father'=>'required',
            'event_id'=>'required',
        ]);

//        $want_to_receive_email = $request->want_to_receive_email;
//        if ($want_to_receive_email == true){
//            $status = 'Y';
//        }else{
//            $status = 'N';
//        }

        $customer_program = new CustomerProgram();
        $customer_program->first_name = $request->first_name;
        $customer_program->last_name = $request->last_name;
        $customer_program->email = $request->email;
        $customer_program->phone = $request->phone;
        $customer_program->ages_of_children = $request->ages_of_children;
        $customer_program->ages_of_father = $request->ages_of_father;
        //$customer_program->want_to_receive_email = $status;
        $customer_program->event_id = $request->event_id;
        $customer_program->save();
        return response()->json([
            'status'=>'success',
            'message'=>'Program Created Successfully'
        ],200);
    }
}
