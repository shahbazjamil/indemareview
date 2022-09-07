<?php

namespace App\Http\Requests\Tasks;

use App\Company;
use App\Http\Requests\CoreRequest;
use App\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreTask extends CoreRequest
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
        Validator::extend('after_equal', function ($attribute, $value, $parameters, $validator) {
            $start_date = Carbon::createFromFormat(company()->date_format, $this->get('start_date'));
            $due_date = Carbon::createFromFormat(company()->date_format, $this->get('due_date'));

            if ($due_date->lessThan($start_date)){
                return false;
            }else{
                return true;
            }
        });

        $setting = Company::with('currency', 'package')->withoutGlobalScope('active')->where('id', Auth::user()->company_id)->first();
        $user = auth()->user();
        $rules = [
            'heading' => 'required',
            'start_date' => 'required',
            'due_date' => 'required|after_equal',
            'priority' => 'required'
        ];

        if($user->can('add_tasks') || $user->hasRole('admin')) {
            $rules['user_id'] = 'required';
            $rules['user_id.*'] = 'required';
        }

        if($this->has('dependent') && $this->dependent == 'yes' && $this->dependent_task_id != '')
        {
            $dependentTask = Task::find($this->dependent_task_id);

            $rules['start_date'] = 'required|after:"'.$dependentTask->due_date->format($setting->date_format).'"';
        }

        if($this->has('repeat') && $this->repeat == 'yes')
        {
            $rules['repeat_cycles'] = 'required|numeric';
        }

        return $rules;
    }

    public function messages() {
        return [
          'project_id.required' => __('messages.chooseProject'),
          'user_id.required' => 'Choose an assignee',
          'due_date.after_equal' => 'The due date must be a date after or equal to start date.'
        ];
    }
}
