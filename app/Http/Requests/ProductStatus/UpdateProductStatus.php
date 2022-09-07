<?php

namespace App\Http\Requests\ProductStatus;

use App\CodeType;
use App\ProductStatus;
use Froiden\LaravelInstaller\Request\CoreRequest;
use Illuminate\Validation\Rule;

class UpdateProductStatus extends CoreRequest
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
       
        $detailID = ProductStatus::where('id', $this->route('product_status'))->first();
        return [
            "status_name" => [
                'required',
                Rule::unique('product_status')->where(function($query) use($detailID) {
                    $query->where('company_id', company()->id);
                    $query->where('id', '<>' ,$detailID->id);
                })
            ],
            'status_color' => 'required'
        ];
    }
}