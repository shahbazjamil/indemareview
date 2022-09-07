<?php

namespace App\Http\Requests\PoSetting;

use App\PoStatus;
use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Validation\Rule;


class UpdatePoStatus extends CoreRequest
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
        $detailID = PoStatus::where('id', $this->route('purchase_order_setting'))->first();
        return [
            //'type' => 'required|alpha|unique:po_status,type,'.$this->route('purchase_order_setting'),
            "type" => [
                'required',
                'alpha',
                Rule::unique('po_status')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
        ];
    }
}
