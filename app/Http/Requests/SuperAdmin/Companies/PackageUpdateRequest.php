<?php

namespace App\Http\Requests\SuperAdmin\Companies;


use App\Http\Requests\SuperAdmin\SuperAdminBaseRequest;

class PackageUpdateRequest extends SuperAdminBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
             'package' => 'required|exists:packages,id',
             'packageType' => 'required|in:monthly,annual'
         ];
        if($this->payment_method != '')
        {
               $rules['pay_date'] = 'required';
        }
        
        //'payment_method' => 'required|exists:offline_payment_methods,id', payment method option by SB
        return $rules;
         
    }
}