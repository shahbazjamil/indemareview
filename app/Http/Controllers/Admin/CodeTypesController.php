<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\CodeType\StoreCodeType;
use App\Http\Requests\CodeType\UpdateCodeType;
use App\CodeType;

class CodeTypesController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Code Types';
        $this->pageIcon = 'ti-settings';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->codeTypes = CodeType::all();
        return view('admin.code-settings.types.index', $this->data);
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
    public function store(StoreCodeType $request)
    {
        $code = new CodeType();
        
        // if CODE empty then set forst three lattter of name
        $location_code = $result = strtoupper(substr(str_replace(' ', '', $request->location_name), 0, 3));
        if(!is_null($request->location_code)) {
            $location_code = $request->location_code;
        }
        
        $code->location_code = $location_code;
        $code->location_name = $request->location_name;
        $code->save();

        $allCodes = CodeType::all();

        $select = '';
        foreach($allCodes as $code){
            $select.= '<option value="'.$code->location_code.'">'.ucwords($code->location_name).'</option>';
        }

        return Reply::successWithData(__('messages.codeTypeAddSuccess'), ['optionData' => $select]);
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
        $this->code = CodeType::findOrFail($id);
        return view('admin.code-settings.types.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCodeType $request, $id)
    {
        $code = CodeType::findOrFail($id);
        
        // if CODE empty then set forst three lattter of name
        $location_code = $result = strtoupper(substr(str_replace(' ', '', $request->location_name), 0, 3));
        if(!is_null($request->location_code)) {
            $location_code = $request->location_code;
        }
        
        $code->location_code = $location_code;
        $code->location_name = $request->location_name;
        $code->save();

        return Reply::success(__('messages.codeTypeUpdateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function createType()
    {
        $this->codeTypes = CodeType::all();
        return view('admin.code-settings.types.create-status', $this->data);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeType(StoreCodeType $request)
    {
        $code = new CodeType();
        $code->location_code = $request->location_code;
        $code->location_name = $request->location_name;
        $code->save();
        $allCodes = CodeType::all();
       
        return Reply::successWithData(__('messages.codeTypeAddSuccess'),['data' => $allCodes]);
    }
    
    
    public function destroy($id)
    {
        CodeType::destroy($id);
        
        $codeTypes = CodeType::all(); 
        return Reply::successWithData(__('messages.codeTypeDeleteSuccess'),['data' => $codeTypes]);
    }

    public function createModal(){
        return view('admin.code-settings.types.create-modal');
    }
}
