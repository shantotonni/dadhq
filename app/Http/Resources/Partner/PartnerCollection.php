<?php

namespace App\Http\Resources\Partner;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PartnerCollection extends ResourceCollection
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
          'data'=>$this->collection->transform(function ($partner){
              return[
                  'id'=>$partner->id,
                  'title'=>$partner->title,
                  'status'=>$partner->status,
                  'image'=>$partner->image,
              ];
          })
      ];
    }
}
