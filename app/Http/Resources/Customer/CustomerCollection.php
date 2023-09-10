<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data'=>$this->collection->transform(function ($customer){
                return [
                    'id'=>$customer->id,
                    'first_name'=>$customer->first_name,
                    'last_name'=>$customer->last_name,
                    'email'=>$customer->email,
                    'phone'=>$customer->phone,
                    'ages_of_children'=>$customer->ages_of_children,
                    'ages_of_father'=>$customer->ages_of_father,
                    'want_to_receive_email'=>$customer->want_to_receive_email,
                    'customer_status'=>$customer->customer_status,
                ];
            })
        ];
    }
}
