<?php

namespace App\Http\Requests\LeadSetting;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeadDataPublic extends CoreRequest
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
            'lead_name' => 'required',
            'lead_email' => 'required',
            'lead_phone' => 'required',
            'lead_message' => 'required',
            'company_id' => 'required'
        ];
    }
}
