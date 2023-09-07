<?php

namespace App\Http\Controllers;

use App\Http\Requests\Program\ProgramStoreRequest;
use App\Http\Resources\Program\ProgramCollection;
use App\Models\Program;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::orderBy('id','desc')->paginate(15);

        return new ProgramCollection($programs);
    }


    public function store(ProgramStoreRequest $request)
    {
        if ($request->has('image')) {
            $image = $request->image;
            $name = uniqid().time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($image)->save(public_path('images/program/').$name);
        } else {
            $name = 'not_found.jpg';
        }

        $program = new Program();
        $program->title = $request->title;
        $program->description = $request->description;
        $program->ordering = $request->ordering;
        $program->image = $name;
        $program->status =  $request->status;
        $program->save();

        return response()->json(['message'=>'Program Created Successfully'],200);
    }

    public function update(Request $request, $id)
    {
        $program = Program::where('id',$request->id)->first();
        $image = $request->image;

        if ($image != $program->image) {
            if ($request->has('image')) {
                //code for remove old file
                if ($program->image != '' && $program->image != null) {
                    $destinationPath = 'images/program/';
                    $file_old = $destinationPath . $program->image;
                    if (file_exists($file_old)) {
                        unlink($file_old);
                    }
                }
                $name = uniqid() . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                Image::make($image)->resize(1600,1000)->save(public_path('images/program/') . $name);
            } else {
                $name = $program->image;
            }
        }else{
            $name = $program->image;
        }

        $program->title = $request->title;
        $program->description = $request->description;
        $program->ordering = $request->ordering;
        $program->image = $name;
        $program->status =  $request->status;
        $program->save();
        return response()->json(['message'=>'Program Updated Successfully'],200);
    }

    public function destroy($id)
    {
        $program = Program::where('id', $id)->first();
        if ($program->image) {
            $destinationPath = '/images/program/';

            $file = public_path('/') . $destinationPath . $program->image;
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $program->delete();
        return response()->json(['message' => 'Program Deleted Successfully']);
    }


    public function search($query)
    {
        return new ProgramCollection(Program::Where('title', 'like', "%$query%")
            ->paginate(10));
    }
}
