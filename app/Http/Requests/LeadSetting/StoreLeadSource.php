<?php

namespace App\Http\Requests\LeadSetting;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadSource extends CoreRequest
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
            //'type' => 'required|unique:lead_sources|alpha',
            "type" => [
                'required',
                Rule::unique('lead_sources')->where(function($query) {
                    $query->where('company_id', company()->id);
                })
            ]
        ];
    }
}
