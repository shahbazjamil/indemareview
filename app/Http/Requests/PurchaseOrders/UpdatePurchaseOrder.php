<?php

namespace App\Http\Requests\PurchaseOrders;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseOrder extends CoreRequest
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
        ];
        return $rules;
    }

}
