<?php

namespace Modules\RestAPI\Http\Controllers;

use Froiden\RestAPI\ApiController;
use Modules\RestAPI\Entities\Product;
use Modules\RestAPI\Http\Requests\Product\IndexRequest;
use Modules\RestAPI\Http\Requests\Product\CreateRequest;
use Modules\RestAPI\Http\Requests\Product\ShowRequest;
use Modules\RestAPI\Http\Requests\Product\UpdateRequest;
use Modules\RestAPI\Http\Requests\Product\DeleteRequest;
use Modules\RestAPI\Http\Requests\MoodBoard\MoodRequest;

use Froiden\RestAPI\ApiResponse;
use Froiden\RestAPI\Exceptions\ApiException;
use Modules\RestAPI\Entities\ClientVendorDetail;
use Modules\RestAPI\Entities\SalescategoryType;
use Modules\RestAPI\Entities\CodeType;
use Modules\RestAPI\Entities\Project;




class ProductController extends ApiBaseController
{

    protected $model = Product::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    
    
    public function getVendors(MoodRequest $request){
        
        $user = api_user();
        $company = $user->company;
        $query = ClientVendorDetail::select('id', 'company_id', 'company_name')->orderBy('company_name', 'ASC');
        $query->where('company_id', '=', $company->id);
        $results = $query->get()->toArray();
        
        return ApiResponse::make(null, $results);
        
    }
    public function getSaleCategories(MoodRequest $request){
        
        $user = api_user();
        $company = $user->company;
        $query = SalescategoryType::select('id', 'company_id', 'salescategory_code', 'salescategory_name');
        $query->where('company_id', '=', $company->id);
        $results = $query->get()->toArray();
        return ApiResponse::make(null, $results);
        
    }
    public function getLocationCodes(MoodRequest $request){
        $user = api_user();
        $company = $user->company;
        $query = CodeType::select('id', 'company_id', 'location_code', 'location_name');
        $query->where('company_id', '=', $company->id);
        $results = $query->get()->toArray();
        return ApiResponse::make(null, $results);
        
    }
    public function getProjects(MoodRequest $request){
        $user = api_user();
        $company = $user->company;
        $query = Project::select('id', 'company_id', 'project_name');
        $query->where('company_id', '=', $company->id);
        $results = $query->get()->toArray();
        return ApiResponse::make(null, $results);
    }
    
    public function filterProducts(MoodRequest $request){
        $user = api_user();
        $company = $user->company;
        $query = Product::select('products.id', 'products.company_id' , 'products.name', 'products.cost_per_unit', 'products.vendor_description', 'products.taxable', 'products.picture');
        $query->where('company_id', '=', $company->id);
        
        
        if(isset($request->vendor_id) && !empty($request->vendor_id)) {
             $query->where('vendor_id', '=', $request->vendor_id);
        }
        
        if(isset($request->sale_category_code) && !empty($request->sale_category_code)) {
            $query->where('item->salesCategory', '=', $request->sale_category_code);
        }
        
        if(isset($request->location_code_id) && !empty($request->location_code_id)) {
            $query->join('product_code_types', 'product_code_types.product_id', '=', 'products.id');
            $query->where('product_code_types.code_type_id', $request->location_code_id);
        }
        
        if(isset($request->project_id) && !empty($request->project_id)) {
            $query->join('product_projects', 'product_projects.product_id', '=', 'products.id');
            $query->where('product_projects.project_id', $request->project_id);
        }
        
        
        $results = $query->get()->toArray();
        return ApiResponse::make(null, $results);
    }
    
}
