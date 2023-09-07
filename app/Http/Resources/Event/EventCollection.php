<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventCollection extends ResourceCollection
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
            'data'=>$this->collection->transform(function ($event){
                return [
                    'id'=>$event->id,
                    'title'=>$event->title,
                    'description'=>$event->description,
                    'event_date'=> date('Y-m-d',strtotime($event->event_date)),
                    'ordering'=>$event->ordering,
                    'status'=>$event->status,
                    'image'=>$event->image,
                ];
            })
        ];
    }
}
