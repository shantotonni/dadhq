<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'=>'required|min:3',
            //'last_name'=>'required|min:3',
            'batch_number'=>'required',
            'roll_no'=>'required',
            //'email'=>'required',
            //'mobile'=>'required',
            //'date_of_birth'=>'required',
            //'nid'=>'required',
           // 'address'=>'required',
            'nationality'=>'required',
            'session_id'=>'required',
            'category_id'=>'required',
            'is_hostel'=>'required',
            'status'=>'required',
        ];
    }
}
