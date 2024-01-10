<?php

namespace App\Http\Controllers;

use App\Http\Resources\Event\EventCollection;
use App\Http\Resources\Program\ProgramCollection;
use App\Models\Event;
use App\Models\Program;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function getAllProgram(){
        $programs = Program::query()->orderBy('ordering','asc')->get();
        return new ProgramCollection($programs);
    }

    public function getAllEvents(){
        $events = Event::query()->orderBy('ordering','asc')->get();
        return new EventCollection($events);
    }

    public function getOurProgramDetails(Request $request){
        $program_details = Program::where('id',$request->id)->first();
        return response()->json([
            'details' => $program_details
        ]);
    }
}
