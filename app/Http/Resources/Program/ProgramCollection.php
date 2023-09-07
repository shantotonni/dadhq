<?php

namespace App\Http\Resources\Program;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProgramCollection extends ResourceCollection
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
            'data'=>$this->collection->transform(function ($program){
                return [
                    'id'=>$program->id,
                    'title'=>$program->title,
                    'description'=>$program->description,
                    'ordering'=>$program->ordering,
                    'status'=>$program->status,
                    'image'=>$program->image,
                ];
            })
        ];
    }
}
