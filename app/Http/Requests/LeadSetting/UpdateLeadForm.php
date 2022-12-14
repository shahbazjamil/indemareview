<?php

namespace App\Http\Requests\LeadSetting;

use Froiden\LaravelInstaller\Request\CoreRequest;

class UpdateLeadForm extends CoreRequest
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
            'field_name' => 'required'
        ];
    }
}
