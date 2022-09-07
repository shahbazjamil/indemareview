<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\CoreRequest;

class UpdateVendorRequest extends CoreRequest
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
            'company_name' => 'required',
            //'vendor_name' => 'required',
            //'vendor_rep_name' => 'required',
            //'rep_email' => 'required',
            //'rep_phone' => 'required',
            //'company_website' => 'required',
            
        ];
    }
}
