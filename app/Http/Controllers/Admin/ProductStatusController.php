<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\ProductStatus\StoreProductStatus;
use App\Http\Requests\ProductStatus\UpdateProductStatus;
use App\ProductStatus;


class ProductStatusController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = ' Product Statuses';
        $this->pageIcon = 'ti-settings';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
         //insert default statsues if not exist
        $statusesArr = [
            ['status_name' => 'Proposed', 'status_color' => '#4600ff'],
            ['status_name' => 'Ordered', 'status_color' => '#ba5a5a'],
            ['status_name' => 'Backordered', 'status_color' => '#ff0000'],
            ['status_name' => 'Shipped', 'status_color' => '#00b46b'],
            ['status_name' => 'Received', 'status_color' => '#ffea00'],
            ['status_name' => 'Installed', 'status_color' => '#fe00ff']
        ];
        
       foreach ($statusesArr as $status) {
            $getStatus = ProductStatus::where('status_name', $status['status_name'])->first();
            if(!$getStatus) {
                $saveStatus = new ProductStatus();
                $saveStatus->status_name = $status['status_name'];
                $saveStatus->status_color = $status['status_color'];
                $saveStatus->company_id = company()->id;
                $saveStatus->save();
            }
       }
        
        $this->statuses = ProductStatus::all();
        return view('admin.product-status.types.index', $this->data);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductStatus $request)
    {
        $productStatus = new ProductStatus();
        $productStatus->status_name = $request->status_name;
        $productStatus->status_color = $request->status_color;
        $productStatus->save();
        
//        $allCodes = ProductStatus::all();
//        $select = '';
//        foreach($allCodes as $code){
//            $select.= '<option value="'.$code->salescategory_code.'">'.ucwords($code->salescategory_name).'</option>';
//        }
        
        return Reply::successWithData('Product status added successfully', ['optionData' => '']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->status = ProductStatus::findOrFail($id);
        return view('admin.product-status.types.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductStatus $request, $id)
    {
        $productStatus = ProductStatus::findOrFail($id);
        
        $productStatus->status_name = $request->status_name;
        $productStatus->status_color = $request->status_color;
        $productStatus->save();

        return Reply::success('Product status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function createStatus()
    {
        $this->statuses = ProductStatus::all();
        return view('admin.product-status.types.create-status', $this->data);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStatus(StoreProductStatus $request)
    {
        $productStatus = new ProductStatus();
        $productStatus->status_name = $request->status_name;
        $productStatus->status_color = $request->status_color;
        $productStatus->save();

        $statuses = ProductStatus::all();
        
        return Reply::successWithData('Product status added successfully',['data' => $statuses]);
    }
    
    
    public function destroy($id)
    {
        ProductStatus::destroy($id);
        $statuses = ProductStatus::all();
        
        return Reply::successWithData('Product status deleted successfully',['data' => $statuses]);
        
    }

    public function createModal(){
        return view('admin.product-status.types.create-modal');
    }
}
