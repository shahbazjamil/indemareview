<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\LeadSetting\StoreLeadForm;
use App\Http\Requests\LeadSetting\UpdateLeadForm;
use App\LeadForm;

class LeadFormSettingController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.leadForm';
        $this->pageIcon = 'ti-settings';
        $this->middleware(function ($request, $next) {
            if(!in_array('leads',$this->user->modules)){
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
        $this->leadForm = LeadForm::all();
        return view('admin.lead-settings.form.index', $this->data);
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
    public function store(StoreLeadForm $request)
    {
        $form = new LeadForm();
        $form->field_name = $request->field_name;
        $form->save();

        $allStatus = LeadForm::all();

        $select = '';
        foreach($allStatus as $sts){
            $select.= '<option value="'.$sts->id.'">'.ucwords($sts->type).'</option>';
        }

        return Reply::successWithData(__('messages.leadFormAddSuccess'), ['optionData' => $select]);
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
        $this->leadForm = LeadForm::findOrFail($id);

        return view('admin.lead-settings.form.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLeadForm $request, $id)
    {
        $type = LeadForm::findOrFail($id);
        $type->field_name = $request->field_name;
        $type->save();

        return Reply::success(__('messages.leadFormUpdateSuccess'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getformData($id)
    {
        $this->leadForm = LeadForm::all();

        return view('admin.lead-settings.form.code', $this->data);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        LeadForm::destroy($id);

        return Reply::success(__('messages.leadFormDeleteSuccess'));
    }

    public function createModal(){
        return view('admin.lead-settings.form.create-modal');
    }
}
