<?php

namespace App\Http\Resources\Instructor;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InstructorCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
          'data'=>$this->collection->transform(function ($instructor){
              return[
                  'id'=>$instructor->id,
                  'name'=>$instructor->name,
                  'mobile'=>$instructor->mobile,
                  'email'=>$instructor->email,
                  'address'=>$instructor->address,
                  'description'=>$instructor->description,
                  'image'=>$instructor->image,
                  'educational_qualification'=>$instructor->educational_qualification,
                  'experience'=>$instructor->experience,
                  'status'=>$instructor->status,
              ];
          })
        ];
    }
}
