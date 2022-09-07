<?php

namespace App\Http\Controllers\Member;

use App\Project;
use App\Helper\Reply;
use App\Currency;
use App\Product;
use App\CodeType;
use Illuminate\Http\Request;
use App\ProductNote;
use App\LocationNote;
use Illuminate\Support\Facades\View;





class MemberProjectProductReviewController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            if (!in_array('purchaseOrders', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

   
    public function show($id)
    {
        $this->project = Project::findorFail($id);
        $this->currencies = Currency::all();
        //$products = Product::where('project_id', $id)->get();
        
        $products = Product::join('product_projects', 'product_projects.product_id', '=', 'products.id')
                 ->where('product_projects.project_id',$this->project->id)
            ->select('products.*');
         $products = $products->get();
        
        $locationCodesData = [];
        
        if($products) {
            foreach ($products as $product) {
                $codes = $product->codes;
                if($codes) {
                    foreach ($codes as $code){
                        $locationCode = CodeType::where('id', $code->code_type_id)->first();
                        if ($locationCode) {
                            $total_notes = 0;
                            if ($locationCode->comments) {
                                $total_notes = $locationCode->comments->count();
                            }
                            $locationCodesData[$locationCode->location_code]['id'] = $locationCode->id;
                            $locationCodesData[$locationCode->location_code]['name'] = $locationCode->location_name;
                            $locationCodesData[$locationCode->location_code]['total_notes'] = $total_notes;
                        }
                    }
                }
            }
        }
        
        $this->products = $products;
        $this->locationCodesData = $locationCodesData;
        
        return view('member.projects.product-review.show', $this->data);
    }
    
    public function detail(Request $request, $productID, $projectID)
    {
        $this->product = Product::find($productID);
        $this->product->afterLoad();
        
        $this->project = Project::find($projectID);
        
        return View::make('member.projects.product-review.detail', $this->data);
    }
    
    public function updateSetting(Request $request, $id){
        
        if($request->projectID) {
            $projectID = $request->projectID;
            
            $project = Project::findorFail($projectID);
            $field_name = $request->field_name;
            $field_val = $request->field_val ? $request->field_val : 0;
            if(!empty($field_name)) {
                $project->$field_name = $field_val;
                $project->save();
            }
            $message = 'Project setting has been updated.';
            return Reply::successWithData($message, ['projectID' => $projectID]);
        }
        return Reply::error('Please select project.');
    }
    
    public function update(Request $request, $id){
        
        if($request->productIDS && count($request->productIDS) > 0) {
            $productIDS = $request->productIDS;
            
            foreach ($productIDS as $productID) {
                $product = Product::find($productID);
                if($product) {
                    $product->is_locked = $request->is_locked ? $request->is_locked: 0;
                    $product->save();
                }
            }
            
            $message = 'Product(s) have been locked.';
            if($request->is_locked == 1) {
                $message = 'Product(s) have been unlocked.';
            }
            return Reply::successWithData($message, ['productIDS' => $productIDS, 'is_locked' => $request->is_locked]);
        }
        
        return Reply::error('Please select product(s).');
    }
    
    public function createFinance(Request $request){
        
        $is_approved = false;
        $projectID = $request->project_id ? $request->project_id: '';
        $createType = $request->create_type ? $request->create_type : '';
       
        
        if($request->productIDS && count($request->productIDS) > 0 && !empty($projectID) && !empty($createType)) {
            $productIDS = $request->productIDS;
            $is_approved = true;
            
            foreach ($productIDS as $productID) {
                $product = Product::find($productID);
                if($product) {
                     if($product->is_approved !=1) {
                         $is_approved = false;
                         break;
                     }
                }
            }
        }
        
        if(!$is_approved) {
            return Reply::error('Please select only approved product(s).');
        }
        
        session()->put('review_products' , $productIDS);
        session()->put('project_id' , $projectID);
        if($createType == 'invoice') {
            return Reply::redirect(route('member.invoices.client'));
        } else {
            return Reply::redirect(route('member.estimates.create'));
        }
        
        
    }
    
    
     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewProductNotes($productId)
    {
        $this->productId = $productId;
        $this->notes = ProductNote::where('product_id',$productId)->get();
        return view('member.projects.product-review.view-notes', $this->data);
    }
    
     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewLocationNotes($id)
    {
        $this->locationtId = $id;
        $this->notes = LocationNote::where('code_type_id',$id)->get();
        return view('member.projects.product-review.view-location-notes', $this->data);
    }

   

    /////
}
