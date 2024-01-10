<?php

namespace App\Http\Resources\Program;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProgramCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data'=>$this->collection->transform(function ($program){
                return [
                    'id'=>$program->id,
                    'title'=>$program->title,
                    'short'=>$program->short,
                    'description'=>$program->description,
                    'ordering'=>$program->ordering,
                    'status'=>$program->status,
                    'image'=>$program->image,
                ];
            })
        ];
    }
}
