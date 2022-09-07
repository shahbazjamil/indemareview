<?php

namespace App\Http\Requests\PoSetting;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePoStatus extends CoreRequest
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
            //'type' => 'required|alpha|unique:po_status'
            "type" => [
                'required',
                'alpha',
                Rule::unique('po_status')->where(function($query) {
                    $query->where('company_id', company()->id);
                })
            ]
        ];
    }
}
