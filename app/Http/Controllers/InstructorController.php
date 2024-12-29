<?php

namespace App\Http\Controllers;

use App\Http\Requests\Instructor\InstructorStoreRequest;
use App\Http\Resources\Instructor\InstructorCollection;
use App\Models\Instructor;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class InstructorController extends Controller
{
    public function index()
    {
        $instructor = Instructor::Orderby('id','desc')->paginate(15);
        return new InstructorCollection($instructor);
    }

    public function store(InstructorStoreRequest $request)
    {
        if ($request->has('image')) {
            $image = $request->image;
            $name = uniqid().time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($image)->save(public_path('images/instructor/').$name);
        } else {
            $name = 'not_found.jpg';
        }

        $instructor = New Instructor();
        $instructor->name = $request->name;
        $instructor->mobile = $request->mobile;
        $instructor->email = $request->email;
        $instructor->address = $request->address;
        $instructor->description = $request->description;
        $instructor->educational_qualification = $request->educational_qualification;
        $instructor->experience = $request->experience;
        $instructor->status = $request->status;
        $instructor->image =$name;
        $instructor->created_at =Carbon::now();
        $instructor->save();
        return response()->json([
            'message' => 'Instructor Info Stored Successfully'
        ],200);
    }

    public function update(Request $request, Instructor $id)
    {

        $instructor = Instructor::where('id',$request->id)->first();
        $image = $request->image;

        if ($image != $instructor->image) {
            if ($request->has('image')) {
                //code for remove old file
                if ($instructor->image != '' && $instructor->image != null) {
                    $destinationPath = 'images/instructor/';
                    $file_old = $destinationPath . $instructor->image;
                    if (file_exists($file_old)) {
                        unlink($file_old);
                    }
                }
                $name = uniqid() . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                Image::make($image)->save(public_path('images/instructor/') . $name);
            } else {
                $name = $instructor->image;
            }
        }else{
            $name = $instructor->image;
        }
        $instructor->name = $request->name;
        $instructor->mobile = $request->mobile;
        $instructor->email = $request->email;
        $instructor->address = $request->address;
        $instructor->description = $request->description;
        $instructor->educational_qualification = $request->educational_qualification;
        $instructor->experience = $request->experience;
        $instructor->status = $request->status;
        $instructor->image =$name;
        $instructor->updated_at =Carbon::now();
        $instructor->save();
        return response()->json([
            'message' => 'Instructor Info Updated Successfully'
        ],200);

    }
    public function show($id)
    {
        $instructors = Instructor::where('id', $id)->first();

        return response()->json([
            'data'=>$instructors
        ]);
    }

    public function destroy($id)
    {
        $instructor = Instructor::where('id', $id)->first();
        if ($instructor->image) {
            $destinationPath = '/images/instructor/';

            $file = public_path('/') . $destinationPath . $instructor->image;
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $instructor->delete();
        return response()->json(['message' => 'Instructor Deleted Successfully']);
    }


    public function search($query)
    {
        return new InstructorCollection(Instructor::Where('name', 'like', "%$query%")
            ->paginate(10));
    }
}
