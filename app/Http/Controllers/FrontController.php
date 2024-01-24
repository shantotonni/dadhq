<?php

namespace App\Http\Controllers;

use App\Http\Resources\Event\EventCollection;
use App\Http\Resources\Instructor\InstructorCollection;
use App\Http\Resources\Partner\PartnerCollection;
use App\Http\Resources\Program\ProgramCollection;
use App\Http\Resources\Slider\SliderCollection;
use App\Models\Event;
use App\Models\Instructor;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Slider;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function getAllSlider(){
        $sliders = Slider::query()->where('status','active')->orderBy('ordering','asc')->get();
        return new SliderCollection($sliders);
    }

    public function getAllProgram(){
        $programs = Program::query()->orderBy('ordering','asc')->get();
        return new ProgramCollection($programs);
    }

    public function getAllEvents(){
        $events = Event::query()->where('status','Active')->orderBy('ordering','asc')->get();
        return new EventCollection($events);
    }

    public function getOurProgramDetails(Request $request){
        $program_details = Program::where('id',$request->id)->first();
        return response()->json([
            'details' => $program_details
        ]);
    }

    public function getAllPartner(){
        $partners = Partner::query()->get();
        return new PartnerCollection($partners);
    }

    public function getInstructor(){
        $instructor = Instructor::query()->get();
        return new InstructorCollection($instructor);
    }
}
