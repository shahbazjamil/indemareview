<?php

namespace App\Http\Requests\CodeType;

use App\CodeType;
use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Validation\Rule;

class UpdateCodeType extends CoreRequest
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
        $detailID = CodeType::where('id', $this->route('codeType'))->first();
        return [
            // 'type' => 'required|unique:ticket_types,type,'.$this->route('ticketType'),
            "location_code" => [
                'required',
                Rule::unique('code_types')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
            'location_name' => 'required'
        ];
    }
}
