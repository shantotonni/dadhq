<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\EventStoreRequest;
use App\Http\Resources\Event\EventCollection;
use App\Models\Event;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('id','desc')->paginate(15);

        return new EventCollection($events);
    }


    public function store(EventStoreRequest $request)
    {
        if ($request->has('image')) {
            $image = $request->image;
            $name = uniqid().time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($image)->save(public_path('images/event/').$name);
        } else {
            $name = 'not_found.jpg';
        }

        $event = new Event();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->event_date = date('Y-m-d',strtotime($request->event_date));
        $event->ordering = $request->ordering;
        $event->image = $name;
        $event->status =  $request->status;
        $event->save();

        return response()->json(['message'=>'Event Created Successfully'],200);
    }

    public function update(Request $request, $id)
    {
        $event = Event::where('id',$request->id)->first();
        $image = $request->image;

        if ($image != $event->image) {
            if ($request->has('image')) {
                //code for remove old file
                if ($event->image != '' && $event->image != null) {
                    $destinationPath = 'images/event/';
                    $file_old = $destinationPath . $event->image;
                    if (file_exists($file_old)) {
                        unlink($file_old);
                    }
                }
                $name = uniqid() . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                Image::make($image)->resize(1600,1000)->save(public_path('images/event/') . $name);
            } else {
                $name = $event->image;
            }
        }else{
            $name = $event->image;
        }

        $event->title = $request->title;
        $event->description = $request->description;
        $event->event_date = date('Y-m-d',strtotime($event->event_date));
        $event->ordering = $request->ordering;
        $event->image = $name;
        $event->status =  $request->status;
        $event->save();
        return response()->json(['message'=>'event Updated Successfully'],200);
    }

    public function destroy($id)
    {
        $event = Event::where('id', $id)->first();
        if ($event->image) {
            $destinationPath = '/images/event/';

            $file = public_path('/') . $destinationPath . $event->image;
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $event->delete();
        return response()->json(['message' => 'Event Deleted Successfully']);
    }


    public function search($query)
    {
        return new EventCollection( Event::Where('title', 'like', "%$query%")
            ->paginate(10));
    }
}
