<?php

namespace App\Http\Controllers\Member;

use App\DataTables\Member\MemberProductsDataTable;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreProductImageRequest;
use App\Project;
use App\Helper\Reply;
use Yajra\DataTables\Facades\DataTables;
use App\Currency;
use App\Product;
use App\SalescategoryType;
use App\CodeType;
use App\ProductSetting;


class MemberProjectProductsController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-basket';
        $this->middleware(function ($request, $next) {
            if (!in_array('projects', $this->user->modules)) {
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
//    public function store(StoreMilestone $request)
//    {
//        $milestone = new ProjectMilestone();
//        $milestone->project_id = $request->project_id;
//        $milestone->milestone_title = $request->milestone_title;
//        $milestone->summary = $request->summary;
//        $milestone->cost = ($request->cost == '') ? '0' : $request->cost;
//        $milestone->currency_id = $request->currency_id;
//        $milestone->status = $request->status;
//        $milestone->save();
//
//        return Reply::success(__('messages.milestoneSuccess'));
//    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MemberProductsDataTable $dataTable, $id)
    {
        $this->project = Project::findorFail($id);
        $this->currencies = Currency::all();
        
        $salescategories = $this->salescategories = SalescategoryType::all();
        $salescategoriesArr = [];
        $salescategoriesArr[] = 'Select Category';
        if($salescategories) {
            foreach ($salescategories as $category) {
                $salescategoriesArr[$category->salescategory_code] = $category->salescategory_name;
            }
        }
        $this->salescategoriesData = json_encode($salescategoriesArr);
        
        $codetypes = $this->codetypes = CodeType::all();
        $codetypesArr = [];
        $codetypesArr[] = 'Select Location';
        if($codetypes) {
            foreach ($codetypes as $codetype) {
                $codetypesArr[$codetype->location_code] = $codetype->location_name;
            }
        }
        $this->codetypesData = json_encode($codetypesArr);
        $this->productSettings = ProductSetting::first();
        
        //return view('member.projects.products.show', $this->data);
        return $dataTable->with('project_id', $id)->render('member.projects.products.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//        $this->milestone = ProjectMilestone::findOrFail($id);
//        $this->currencies = Currency::all();
//        return view('member.projects.milestones.edit', $this->data);
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(StoreMilestone $request, $id)
//    {
//        $milestone = ProjectMilestone::findOrFail($id);
//        $milestone->project_id = $request->project_id;
//        $milestone->milestone_title = $request->milestone_title;
//        $milestone->summary = $request->summary;
//        $milestone->cost = ($request->cost == '') ? '0' : $request->cost;
//        $milestone->currency_id = $request->currency_id;
//        $milestone->status = $request->status;
//        $milestone->save();
//
//        return Reply::success(__('messages.milestoneSuccess'));
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        ProjectMilestone::destroy($id);
//        return Reply::success(__('messages.deleteSuccess'));
//    }

    public function data($id)
    {
        //$products = Product::where('project_id', $id)
        $products = Product::join('product_projects', 'product_projects.product_id', '=', 'products.id')
                ->where('product_projects.project_id', $id)
                ->get();
        
        //        if (!is_null($this->project_id)) {
//            //$model->where('project_id', $this->project_id);
//             $model->join('product_projects', 'product_projects.product_id', '=', 'products.id');
//             $model->where('product_projects.project_id', $this->project_id);
//        }
        
        

        return DataTables::of($products)
            ->addColumn('action', function ($row) {
               $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('member.products.edit', [$row->id]). '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                  <li><a href="'. route("member.products.download", $row->id).'"><i class="fa fa-file-pdf-o"></i> ' . trans('app.download') . '</a></li>';
                $action .= '</ul> </div>';
                
                //<li><a href="javascript:;"  data-user-id="' . $row->id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>

                return $action;
            })
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->editColumn('pPicture', function ($row){
                if(!empty($row->picture)) {
                    $pictures = json_decode($row->picture);
                    if($pictures) {
                        $image =  asset('user-uploads/products/'.$row->id.'/'.$pictures[0].'');
                        return  '<img src="' . $image . '" alt="product"  width="50" height="50">';
                    }
                    return '';
                } 
                return '';
            })
            
             ->editColumn('projectName', function ($row) use($id) {
                    $return = '';

                    $project = Project::where('id', $id)->first();
                    if($project) {
                        return ucfirst($project->project_name);
                    }
                    
                 return $return;
                //return $row->project_id;
            })
            ->editColumn('locationCode', function ($row) {
               $item = json_decode($row->item);
               if(isset($item->locationCode) && !empty($item->locationCode)) {
                    $locationCode = CodeType::where('location_code', $item->locationCode)->first();
                    if($locationCode) {
                        return ucfirst($locationCode->location_name);
                    }
                    return '';
                    
               }
               return '';
            })
            ->editColumn('salesCategory', function ($row) {
               $item = json_decode($row->item);
               if(isset($item->salesCategory) && !empty($item->salesCategory)) {
                    $salesCategory = SalescategoryType::where('salescategory_code', $item->salesCategory)->first();
                    if($salesCategory) {
                        return ucfirst($salesCategory->salescategory_name);
                    }
                    return '';
                    
               }
               return '';
            })
            
            ->editColumn('manufacturer', function ($row) {
                return ucfirst($row->manufacturer);
            })
             ->editColumn('dimensions', function ($row) {
                return ($row->dimensions);
            })
            ->editColumn('materials', function ($row) {
                return ($row->materials);
            })
            ->editColumn('vendor_description', function ($row) {
                return ($row->vendor_description);
            })
              ->editColumn('notes', function ($row) {
                return ($row->notes);
            })
            ->editColumn('cost_per_unit', function ($row) {
                return ($row->cost_per_unit);
            })
            ->addIndexColumn()
           ->rawColumns(['pPicture','action'])
            ->make(true);
    }

//    public function detail($id)
//    {
//        $this->milestone = ProjectMilestone::findOrFail($id);
//        return view('member.projects.milestones.detail', $this->data);
//    }

    /////
}
