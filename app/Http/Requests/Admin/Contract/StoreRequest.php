<?php

namespace App\Http\Requests\Admin\Contract;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

use App\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreRequest extends CoreRequest
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
        $setting = Company::with('currency', 'package')->withoutGlobalScope('active')->where('id', Auth::user()->company_id)->first();
        return [
            'client' => 'required',
            'subject' => 'required',
            'amount' => 'numeric|nullable',
            'contract_type' => 'required|exists:contract_types,id',
            //'start_date' => 'required|date_format:' . $setting->date_format,
            //'end_date' => 'required|date_format:' . $setting->date_format,
            //'start_date' => 'required|date',
            //'end_date' => 'required|date',
            'start_date' => 'required',
        ];
    }
}
