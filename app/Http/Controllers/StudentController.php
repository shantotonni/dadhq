<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StudentRequest;
use App\Http\Resources\SheduleCollection;
use App\Http\Resources\Student\StudentCollection;
use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\Student\StudentScheduleCollection;
use App\Models\SessionFee;
use App\Models\Sessions;
use App\Models\Student;
use App\Models\StudentBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->sessionId;
        $roll_number = $request->roll_number;

        $students = Student::query()->with('session','category');
        if (!empty($sessionId)){
            $students = $students->where('session_id',$sessionId);
        }
        if (!empty($roll_number)){
            $students = $students->where('roll_no',$roll_number);
        }

        $students = $students->orderBy('student_id','desc')->where('status','Y')->paginate(15);
        return new StudentCollection($students);
    }

    public function store(StudentRequest $request)
    {
        DB::beginTransaction();
        try {
            $exist_student = Student::where('roll_no',$request->roll_no)->where('session_id',$request->session_id)->where('batch_number',$request->batch_number)->exists();
            if ($exist_student){
                return response()->json([
                    'status'=>400,
                    'message'=>'Already Added'
                ],400);
            }
            $schedule_data = $request->schedule_data;

            $student = new Student();
            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->roll_no = $request->roll_no;
            $student->batch_number = $request->batch_number;
            $student->email = $request->email;
            $student->mobile = $request->mobile;
            $student->date_of_birth = date('Y-m-d',strtotime($request->date_of_birth));
            $student->nid = $request->nid;
            $student->address = $request->address;
            $student->nationality = $request->nationality;
            $student->session_id = $request->session_id;
            $student->category_id = $request->category_id;
            $student->is_hostel = $request->is_hostel;
            $student->status = $request->status;

            $student->created_by = Auth::user()->user_id;
            $student->created_by_ip = $_SERVER['REMOTE_ADDR'];

            if ($student->save()) {

                foreach ($schedule_data as $value){
                    $schedule = new StudentBill();
                    $schedule->student_id = $student->student_id;
                    $schedule->expected_payment_date = $value['expected_payment_date'];
                    $schedule->payment_head = $value['year'];
                    $schedule->amount = $value['amount'];
                    $schedule->paid_amount = 0;
                    $schedule->due_amount = $value['amount'];
                    $schedule->ordering = $value['ordering'];
                    $schedule->created_by = Auth::user()->user_id;
                    $schedule->created_by_ip = $_SERVER['REMOTE_ADDR'];
                    $schedule->save();
                }

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Student created successfully'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'=>400,
                'message'=>$e->getMessage()
            ],400);
        }
    }

    public function update(StudentRequest $request,$id)
    {
        DB::beginTransaction();
        try {
            $schedule_data = $request->schedule_data;

            $student = Student::find($id);
            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->roll_no = $request->roll_no;
            $student->batch_number = $request->batch_number;
            $student->email = $request->email;
            $student->mobile = $request->mobile;
            $student->date_of_birth = $request->date_of_birth;
            $student->nid = $request->nid;
            $student->address = $request->address;
            $student->nationality = $request->nationality;
            $student->session_id = $request->session_id;
            $student->category_id = $request->category_id;
            $student->status = $request->status;
            $student->is_hostel = $request->is_hostel;

            $student->updated_by = Auth::user()->user_id;
            $student->updated_by_ip = $_SERVER['REMOTE_ADDR'];

            if ($student->save()) {
                StudentBill::where('student_id',$id)->delete();
                foreach ($schedule_data as $value){
                    $schedule = new StudentBill();
                    $schedule->student_id = $student->student_id;
                    $schedule->expected_payment_date = $value['expected_payment_date'];
                    $schedule->payment_head = $value['year'];
                    $schedule->amount = $value['amount'];
                    $schedule->paid_amount = 0;
                    $schedule->due_amount = $value['amount'];
                    $schedule->ordering = $value['ordering'];
                    $schedule->save();
                }

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Student Updated successfully'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'=>400,
                'message'=>$e->getMessage()
            ],400);
        }
    }

    public function destroy($id)
    {
        $student = Student::where('student_id',$id)->first();
        $student->status = 'N';
        $student->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Student Deleted successfully'
        ]);
    }

    public function search($query)
    {
        return new StudentCollection(Student::where('first_name','LIKE',"%$query%")->orWhere('last_name','LIKE',"%$query%")->latest()->paginate(20));
    }

    public function getScheduleData($session_id, $category_id){
        $session = Sessions::where('session_id',$session_id)->first();
        $session_fee = SessionFee::where('session_id',$session_id)->where('category_id', $category_id)->with(['year','session','category','category.currency'])->get();
        return response()->json([
            'data'=>new SheduleCollection($session_fee),
            'session'=>$session
        ]);
    }

    public function getStudentWiseScheduleData($student_id){
        $student_bill = StudentBill::where('student_id',$student_id)->with('student.session','student.category','student.category.currency')->get();
        return new StudentScheduleCollection($student_bill);
    }

    public function getStudentDetails(Request $request){
        $session_id = $request->session_id;
        $roll_no = $request->roll_no;
        $student = Student::where('session_id',$session_id)->where('roll_no',$roll_no)->with('session','category','category.currency')->first();
        return new StudentResource($student);
    }
}
