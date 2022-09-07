<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\CoreRequest;

class UpdateProject extends CoreRequest
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
        
        // solo plan project member is requred 
        if(company()->package_id == 7) {
            $rules = [
                'project_name' => 'required',
                'start_date' => 'required',
                //'user_id.0' => 'required'
            ];
            
        } else {
            $rules = [
                'project_name' => 'required',
                'start_date' => 'required',
                //'client_id.0' => 'required'
            ];
        }
        

        if(!$this->has('without_deadline')){
            $rules['deadline'] = 'required';
        }

        return $rules;
    }
    
     public function messages() {
        return [
            //'client_id.0.required' =>'Select at least one client.'
        ];
    }
}
