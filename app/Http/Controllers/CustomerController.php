<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerStoreRequest;
use App\Http\Resources\Customer\CustomerCollection;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderby('id','desc')->paginate(15);
        return new CustomerCollection($customers);
    }

    public function store(CustomerStoreRequest $request)
    {
        $customer = new Customer;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->ages_of_children = $request->ages_of_children;
        $customer->ages_of_father = $request->ages_of_father;
        $customer->want_to_receive_email = $request->want_to_receive_email;
        $customer->customer_status = $request->customer_status;
        $customer->save();
        return response()->json(['message'=>'Customer info Successfully stored',200]);

    }

    public function update(Request $request, $id)
    {

        $customer = Customer::where('id',$request->id)->first();
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->ages_of_children = $request->ages_of_children;
        $customer->ages_of_father = $request->ages_of_father;
        $customer->want_to_receive_email = $request->want_to_receive_email;
        $customer->customer_status = $request->customer_status;
        $customer->save();
        return response()->json(['message'=>'Customer info Successfully updated',200]);
    }

    public function destroy( $id)
    {
        Customer::where('id', $id)->delete();
        return response()->json(['message'=>'Customer info Successfully Deleted',200]);
    }
}
