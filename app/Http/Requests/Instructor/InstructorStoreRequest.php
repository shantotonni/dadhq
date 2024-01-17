<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class InstructorStoreRequest extends FormRequest
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
        return[
            'name'=>'required',
            'mobile'=>'required',
            'email'=>'required',
            'address'=>'required',
            'description'=>'required',
            'image'=>'required',
            'educational_qualification'=>'required',
            'experience'=>'required',
            'status'=>'required',
        ];
    }
}
