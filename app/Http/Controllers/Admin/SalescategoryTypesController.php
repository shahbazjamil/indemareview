<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\SalescategoryType\StoreSalescategoryType;
use App\Http\Requests\SalescategoryType\UpdateSalescategoryType;
use App\SalescategoryType;

class SalescategoryTypesController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Sales Categories';
        $this->pageIcon = 'ti-settings';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->salescategories = SalescategoryType::all();
        return view('admin.salescategory-settings.types.index', $this->data);
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
    public function store(StoreSalescategoryType $request)
    {
        $code = new SalescategoryType();
        
        // if CODE empty then set forst three lattter of name
        $salescategory_code = $result = strtoupper(substr(str_replace(' ', '', $request->salescategory_name), 0, 3));
        if(!is_null($request->salescategory_code)) {
            $salescategory_code = $request->salescategory_code;
        }
        
        $code->salescategory_code = $salescategory_code;
        $code->salescategory_name = $request->salescategory_name;
        $code->salescategory_markup = $request->salescategory_markup?$request->salescategory_markup:0;
        $code->save();

        $allCodes = SalescategoryType::all();

        $select = '';
        foreach($allCodes as $code){
            $select.= '<option value="'.$code->salescategory_code.'">'.ucwords($code->salescategory_name).'</option>';
        }

        return Reply::successWithData(__('messages.salesCategoryAddSuccess'), ['optionData' => $select]);
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
        $this->salescategory = SalescategoryType::findOrFail($id);
        return view('admin.salescategory-settings.types.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSalescategoryType $request, $id)
    {
        $code = SalescategoryType::findOrFail($id);
        
        // if CODE empty then set forst three lattter of name
        $salescategory_code = $result = strtoupper(substr(str_replace(' ', '', $request->salescategory_name), 0, 3));
        if(!is_null($request->salescategory_code)) {
            $salescategory_code = $request->salescategory_code;
        }
        
        $code->salescategory_code = $salescategory_code;
        $code->salescategory_name = $request->salescategory_name;
        $code->salescategory_markup = $request->salescategory_markup?$request->salescategory_markup:0;
        
        $code->save();

        return Reply::success(__('messages.salesCategoryUpdateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function createCategory()
    {
        $this->salescategories = SalescategoryType::all();
        return view('admin.salescategory-settings.types.create-status', $this->data);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(StoreSalescategoryType $request)
    {
        $code = new SalescategoryType();
        $code->salescategory_code = $request->salescategory_code;
        $code->salescategory_name = $request->salescategory_name;
        $code->salescategory_markup = $request->salescategory_markup?$request->salescategory_markup:0;
        $code->save();

        $allCodes = SalescategoryType::all();
        
        return Reply::successWithData(__('messages.categoryAddSuccess'),['data' => $allCodes]);
    }
    
    
    public function destroy($id)
    {
        SalescategoryType::destroy($id);
        
        $salescategories = SalescategoryType::all();
        return Reply::successWithData(__('messages.categoryDeleteSuccess'),['data' => $salescategories]);
        
    }

    public function createModal(){
        return view('admin.salescategory-settings.types.create-modal');
    }
}
