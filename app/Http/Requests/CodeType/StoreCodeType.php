<?php

namespace App\Http\Requests\CodeType;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCodeType extends CoreRequest
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
            // 'type' => 'required|unique:code_types'
//            "location_code" => [
//                'required',
//                Rule::unique('code_types')->where(function($query) {
//                    $query->where('company_id', company()->id);
//                })
//            ],
            'location_name' => 'required'

        ];
    }
}
