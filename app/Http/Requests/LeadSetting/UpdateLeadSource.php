<?php

namespace App\Http\Requests\LeadSetting;

use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Validation\Rule;
use App\LeadSource;

class UpdateLeadSource extends CoreRequest
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
        $detailID = LeadSource::where('id', $this->route('lead_source_setting'))->first();
        return [
            //'type' => 'required|alpha|unique:lead_sources,type,'.$this->route('lead_source_setting'),
             "type" => [
                'required',
                Rule::unique('lead_sources')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
        ];
    }
}
