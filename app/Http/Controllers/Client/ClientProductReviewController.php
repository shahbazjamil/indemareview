<?php

namespace App\Http\Controllers\Client;

use App\Helper\Reply;
use App\ModuleSetting;
use App\Project;
use App\SubTask;
use App\Task;
use App\TaskboardColumn;
use App\TaskCategory;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Product;
use App\CodeType;
use App\InvoiceSetting;
use App\ProductNote;
use App\LocationNote;
use App\Http\Requests\Product\StoreProductNote;
use Illuminate\Support\Facades\View;

class ClientProductReviewController extends ClientBaseController
{
    use ProjectProgress;
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';
//        $this->middleware(function ($request, $next) {
//            if(!in_array('tasks',$this->user->modules)){
//                abort(403);
//            }
//            return $next($request);
//        });

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $this->project = Project::findorFail($id);
        $this->invoiceSetting = InvoiceSetting::first();
        
        
        $products = Product::join('product_projects', 'product_projects.product_id', '=', 'products.id')
                 ->where('product_projects.project_id',$this->project->id)
            ->select('products.*');
         $products = $products->get();
        
        $locationCodesData = [];
        
        if ($products) {
            foreach ($products as $product) {
                $itemObj = json_decode($product->item);
                if (isset($itemObj->locationCode) && !empty($itemObj->locationCode)) {
                    $locationCode = CodeType::where('location_code', $itemObj->locationCode)->first();
                    if ($locationCode) {
                        $total_notes = 0;
                        if ($locationCode->comments) {
                            $total_notes = $locationCode->comments->count();
                        }
                        $locationCodesData[$itemObj->locationCode]['id'] = $locationCode->id;
                        $locationCodesData[$itemObj->locationCode]['name'] = $locationCode->location_name;
                        $locationCodesData[$itemObj->locationCode]['total_notes'] = $total_notes;
                    }
                }
            }
        }

        
        $this->products = $products;
        $this->locationCodesData = $locationCodesData;
        
        
        return view('client.product-review.show', $this->data);
    }
    
     public function detail(Request $request, $productID, $projectID)
    {
        $this->product = Product::find($productID);
        $this->product->afterLoad();
        
        $this->project = Project::find($projectID);
        
        return View::make('client.product-review.detail', $this->data);
    }
    
    public function update(Request $request, $id){
        
        
        if($request->productIDS && count($request->productIDS) > 0) {
            $productIDS = $request->productIDS;
            
            foreach ($productIDS as $productID) {
                $product = Product::find($productID);
                if($product) {
                    $product->is_approved = $request->is_approved ? $request->is_approved: 0;
                    $product->save();
                }
            }
            
            $message = 'Product(s) have been declined.';
            if($request->is_approved == 1) {
                $message = 'Product(s) have been approved.';
            }
            return Reply::successWithData($message, ['productIDS' => $productIDS, 'is_approved' => $request->is_approved]);
        }
        
        return Reply::error('Please select product(s).');
    }
    
     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProductNotes($productId)
    {
        //Product::find($productID);
        $this->productId = $productId;
        return view('client.product-review.create-notes', $this->data);
    }
    
     
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProductNotes(StoreProductNote $request)
    {
        $note = new ProductNote();
        $note->note = $request->note;
        $note->product_id = $request->product_id;
        $note->user_id = $this->user->id;
        $note->save();
        
        return Reply::successWithData('Added Successfully', ['productIDS' => '', 'is_approved' => '']);

        
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createLocationNotes($id)
    {
        //Product::find($productID);
        //$locationCode = CodeType::where('location_code', $codeType)->first();
        $this->locationtId = $id;
        return view('client.product-review.create-location-notes', $this->data);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function storeLocationNotes(StoreProductNote $request)
    {
        $note = new LocationNote();
        $note->note = $request->note;
        $note->code_type_id = $request->locationt_id;
        $note->user_id = $this->user->id;
        $note->save();
        
        return Reply::successWithData('Added Successfully', ['productIDS' => '', 'is_approved' => '']);

        
    }

    

   
}
