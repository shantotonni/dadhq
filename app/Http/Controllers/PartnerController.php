<?php

namespace App\Http\Controllers;

use App\Http\Requests\Partner\PartnerStoreRequest;
use App\Http\Resources\Partner\PartnerCollection;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;


class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('id','desc')->paginate(15);
        return new PartnerCollection($partners);
    }


    public function store(PartnerStoreRequest $request)
    {
        if ($request->has('image')) {
            $image = $request->image;
            $name = uniqid().time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($image)->save(public_path('images/partner/').$name);
        } else {
            $name = 'not_found.jpg';
        }

        $partner = new Partner();
        $partner->title = $request->title;
        $partner->image = $name;
        $partner->status =  $request->status;
        $partner->created_at = Carbon::now();
        $partner->save();

        return response()->json(['message'=>'Partner Created Successfully'],200);
    }

    public function update(Request $request, $id)
    {

        $partner = Partner::where('id',$request->id)->first();
        $image = $request->image;

        if ($image != $partner->image) {
            if ($request->has('image')) {
                if ($partner->image != '' && $partner->image != null) {
                    $destinationPath = 'images/partner/';
                    $file_old = $destinationPath . $partner->image;
                    if (file_exists($file_old)) {
                        unlink($file_old);
                    }
                }
                $name = uniqid() . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                Image::make($image)->save(public_path('images/partner/') . $name);
            } else {
                $name = $partner->image;
            }
        }else{
            $name = $partner->image;
        }

        $partner->title = $request->title;
        $partner->status = $request->status;
        $partner->image = $name;
        $partner->updated_at = Carbon::now();
        $partner->save();
        return response()->json(['message'=>'Partner Updated Successfully'],200);
    }

    public function destroy($id)
    {
        $partner = Partner::where('id', $id)->first();
        if ($partner->image) {
            $destinationPath = '/images/partner/';

            $file = public_path('/') . $destinationPath . $partner->image;
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $partner->delete();
        return response()->json(['message' => 'Partner Deleted Successfully']);
    }
    public function search($query)
    {
        return new PartnerCollection(Partner::Where('title', 'like', "%$query%")
            ->paginate(10));
    }
}
