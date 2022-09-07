<?php

namespace App\Http\Requests\PurchaseOrders;

use App\PurchaseOrder;
use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StorePurchaseOrder extends CoreRequest
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
        

        $rules = [
            'vendor_id' => 'required',
            'purchase_order_date' => 'required',
            'address' => 'required',
            'email' => 'required',
            'contact' => 'required',
            'company' => 'required',
            'specification_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx,xls,xlsx,webp,rtf',
            'purchase_order_number' => Rule::unique('purchase_orders')->where(function ($query) {
                return $query->where('company_id', company()->id);
            })
        ];

        return $rules;
    }
}
