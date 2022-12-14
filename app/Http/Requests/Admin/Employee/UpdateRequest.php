<?php

namespace App\Http\Requests\Admin\Employee;

use App\EmployeeDetails;
use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends CoreRequest
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
        $detailID = EmployeeDetails::where('user_id', $this->route('employee'))->first();
        return [
//            'employee_id' => 'required|unique:employee_details,employee_id,'.$detailID->id,
            "employee_id" => [
                'required',
                Rule::unique('employee_details')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
            'email' => 'required|unique:users,email,'.$this->route('employee'),
            'slack_username' => 'nullable|unique:employee_details,slack_username,'.$detailID->id,
            'name'  => 'required',
            'hourly_rate' => 'nullable|numeric',
            'department' => 'required',
            'designation' => 'required',
            'address' => 'required',
        ];
    }
}
