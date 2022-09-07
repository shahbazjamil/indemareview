<?php

namespace App\Http\Requests\SalescategoryType;

use App\CodeType;
use App\SalescategoryType;
use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Validation\Rule;

class UpdateSalescategoryType extends CoreRequest
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
       
        $detailID = SalescategoryType::where('id', $this->route('salescategoryType'))->first();
        return [
            // 'type' => 'required|unique:ticket_types,type,'.$this->route('ticketType'),
            "salescategory_code" => [
                'required',
                Rule::unique('salescategory_types')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
            'salescategory_name' => 'required',
            'salescategory_markup' => 'required'
        ];
    }
}
