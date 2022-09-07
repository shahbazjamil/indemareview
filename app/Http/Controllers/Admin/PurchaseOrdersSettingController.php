<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\PoSetting\StorePoStatus;
use App\Http\Requests\PoSetting\UpdatePoStatus;
use App\PoStatus;

class PurchaseOrdersSettingController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Purchase Orders Status';
        $this->pageIcon = 'ti-settings';
        $this->middleware(function ($request, $next) {
            if(!in_array('purchaseOrders',$this->user->modules)){
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
        $this->poStatus = PoStatus::all();
        return view('admin.po-settings.status.index', $this->data);
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
    public function store(StorePoStatus $request)
    {
        $status = new PoStatus();
        $status->type = $request->type;
        $status->save();

        $allStatus = PoStatus::all();

        $select = '';
        foreach($allStatus as $sts){
            $select.= '<option value="'.$sts->id.'">'.ucwords($sts->type).'</option>';
        }

        return Reply::successWithData(__('messages.poStatusAddSuccess'), ['optionData' => $select]);
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
        $this->status = PoStatus::findOrFail($id);

        return view('admin.po-settings.status.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePoStatus $request, $id)
    {
        $type = PoStatus::findOrFail($id);
        $type->type = $request->type;
        $type->save();

        return Reply::success(__('messages.poStatusUpdateSuccess'));
    }
    
    public function createStatus()
    {
        $this->poStatus = PoStatus::all();
        return view('admin.po-settings.status.create-status', $this->data);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStatus(StorePoStatus $request)
    {
        $status = new PoStatus();
        $status->type = $request->type;
        $status->save();
        
        $poStatus =  PoStatus::all();
        return Reply::successWithData(__('messages.poStatusAddSuccess'),['data' => $poStatus]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PoStatus::destroy($id);
        $poStatus =  PoStatus::all();
        
        return Reply::successWithData(__('messages.poStatusAddSuccess'),['data' => $poStatus]);
    }

    public function createModal(){
        return view('admin.po-settings.status.create-modal');
    }
}
