<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\ContractTemplate\StoreContractTemplate;
use App\ContractTemplate;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminContractTemplateController extends AdminBaseController
{

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.contractTemplate';
        $this->pageIcon = 'icon-layers';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin.contract-template.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.contract-template.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractTemplate $request) {
        $template = new ContractTemplate();
        $template->template_name = $request->template_name;

        if ($request->template_summary) {
            $template->template_summary = $request->template_summary;
        }
        
        $template->save();

        return Reply::redirect(route('admin.contract-template.index'), __('modules.contractTemplate.contractUpdated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $this->template = ContractTemplate::findOrFail($id);
        return view('admin.contract-template.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $this->template = ContractTemplate::findOrFail($id);
        return view('admin.contract-template.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreContractTemplate $request, $id) {
        
        $template = ContractTemplate::findOrFail($id);
        $template->template_name = $request->template_name;
        
        if ($request->template_summary) {
            $template->template_summary = $request->template_summary;
        }

        $template->save();

        return Reply::redirect(route('admin.contract-template.index'), __('modules.contractTemplate.contractAdded'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        ContractTemplate::destroy($id);
        return Reply::success(__('modules.contractTemplate.contractDeleted'));
    }

    public function data(Request $request) {
        $templates = ContractTemplate::select('id', 'template_name')->get();

        return DataTables::of($templates)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.contract-template.edit', [$row->id]) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                      <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
           
            ->editColumn('template_name', function ($row) {
                //return '<a href="' . route('admin.contract-template.show', $row->id) . '">' . ucfirst($row->template_name) . '</a>';
                return ucfirst($row->template_name);
            })
            ->addIndexColumn()
            ->rawColumns(['template_name', 'action'])
            ->make(true);
    }
}
