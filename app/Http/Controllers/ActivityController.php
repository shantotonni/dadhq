<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

class ActivityController extends Controller
{
    function __construct() {
        Config::set('jwt.user', Customer::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ]]);
    }

    public function joinProgram(Request $request){
        $customer = JWTAuth::parseToken()->authenticate();
        $customer_program = new CustomerProgram();
        $customer_program->customer_id = $customer->id;
        $customer_program->program_id = $request->ProgramId;
        $customer_program->save();
        return response()->json(['message'=>'Program Created Successfully'],200);
    }
}
