<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\CoreRequest;

class StoreProductRequest extends CoreRequest
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
        //if ($this->tabId == 'item')
            return [
                'name' => 'required',
                'description' => 'required',
                'vendor_id' => 'required',
                'salesCategory' => 'required',
                'quantity' => 'required|numeric|min:0|not_in:0'
            ];
        //else return [];
    }
}
