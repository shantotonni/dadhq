<?php

namespace App\Http\Resources\Instructor;

use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    public function toArray($request)
    {
        return[
            'id'=>$this->id,
            'name'=>$this->name,
            'mobile'=>$this->mobile,
            'email'=>$this->email,
            'address'=>$this->address,
            'description'=>$this->description,
            'image'=>$this->image,
            'educational_qualification'=>$this->educational_qualification,
            'experience'=>$this->experience,
            'status'=>$this->status,
        ];
    }
}
