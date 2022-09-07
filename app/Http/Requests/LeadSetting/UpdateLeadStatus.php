<?php

namespace App\Http\Requests\LeadSetting;

use App\LeadStatus;
use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Validation\Rule;


class UpdateLeadStatus extends CoreRequest
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
        $detailID = LeadStatus::where('id', $this->route('lead_status_setting'))->first();
        return [
            //'type' => 'required|alpha|unique:lead_status,type,'.$this->route('lead_status_setting'),
            "type" => [
                'required',
                Rule::unique('lead_status')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
        ];
    }
}
