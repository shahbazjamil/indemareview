<?php

namespace App\Http\Controllers\Admin;

use App\Project;
use App\Http\Requests\Room\StoreRoom;
use App\ProjectRoom;
use App\ProjectRoomProduct;
use App\Product;
use App\Helper\Reply;
use Yajra\DataTables\Facades\DataTables;
use App\Currency;

class ManageProjectRoomsController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';
//        $this->middleware(function ($request, $next) {
//            if (!in_array('projects', $this->user->modules)) {
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoom $request)
    {
        $room = new ProjectRoom();
        $room->project_id = $request->project_id;
        $room->room_title = $request->room_title;
        $room->summary = $request->summary;
        //$room->total_cost = ($request->total_cost == '') ? '0' : $request->total_cost;
        $room->save();
        
        
         $total_cost = 0;
        if(is_array($request->product_id) && count($request->product_id) > 0) {
            foreach ($request->product_id as $product_id) {
                $product = Product::find($product_id);
                
                if(isset($product->cost_per_unit) && !empty($product->cost_per_unit)){
                    $total_cost +=$product->cost_per_unit;
                }
                //old formula no more user
//                $item = json_decode($product->item);
//                if(isset($item->totalEstimatedCost) && !empty($item->totalEstimatedCost)){
//                    $total_cost +=$item->totalEstimatedCost;
//                }
                //end
                
                $roomProduct = new ProjectRoomProduct();
                $roomProduct->room_id = $room->id;
                $roomProduct->product_id = $product_id;
                $roomProduct->save();
            }
        }
        
        $room->total_cost = $total_cost;
        $room->save();

        return Reply::success(__('messages.addSuccess'));
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
        $this->products = Product::all();
        return view('admin.projects.rooms.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $this->room = ProjectRoom::findOrFail($id);
        $this->products = Product::all();
        $this->room_products = ProjectRoomProduct::where('room_id', $id)->get();
        
        $room_product_ids = array();
        if($this->room_products) {
            foreach ($this->room_products as $product) {
                $room_product_ids[] = $product->product_id;
            }
        }
        $this->room_product_ids = $room_product_ids;
        
        
        return view('admin.projects.rooms.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRoom $request, $id)
    {
        
        
        $room = ProjectRoom::findOrFail($id);
        $room->project_id = $request->project_id;
        $room->room_title = $request->room_title;
        $room->summary = $request->summary;
        //$room->total_cost = ($request->total_cost == '') ? '0' : $request->total_cost;
        ProjectRoomProduct::where('room_id', $id)->delete();
        $total_cost = 0;
        if(is_array($request->product_id) && count($request->product_id) > 0) {
            foreach ($request->product_id as $product_id) {
                $product = Product::find($product_id);
                
                $item = json_decode($product->item);
                if(isset($item->totalEstimatedCost) && !empty($item->totalEstimatedCost)){
                    $total_cost +=$item->totalEstimatedCost;
                }
                
                $roomProduct = new ProjectRoomProduct();
                $roomProduct->room_id = $id;
                $roomProduct->product_id = $product_id;
                $roomProduct->save();
            }
        }
        
        $room->total_cost = $total_cost;
        
        $room->save();
        
        
        
//        $teetime = ProjectRoomProduct::where('room_id', '=', $formattedDate)->firstOrFail();
//        $teetime->destroy();

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ProjectRoom::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

    public function data($id)
    {
        $rooms = ProjectRoom::where('project_id', $id)->get();

        return DataTables::of($rooms)
            ->addColumn('action', function ($row) {
                return '<a href="javascript:;" class="btn btn-info btn-circle edit-room"
                        data-toggle="tooltip" data-room-id="' . $row->id . '"  data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                        <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                        data-toggle="tooltip" data-room-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn('room_title', function ($row) {
                return '<a href="javascript:;" class="room-detail" data-room-id="' . $row->id . '">' . ucfirst($row->room_title) . '</a>';
            })
            ->editColumn('cost', function ($row) {
                if (!is_null($row->currency_id)) {
                    return $row->currency->currency_symbol . $row->cost;
                }
                return $row->cost;
            })
             ->addIndexColumn()
            ->rawColumns(['action', 'room_title'])
            ->removeColumn('project_id')
            ->make(true);
    }

    public function detail($id)
    {
        $this->room = ProjectRoom::findOrFail($id);
         $this->room_products = ProjectRoomProduct::where('room_id', $id)->get();
        return view('admin.projects.rooms.detail', $this->data);
    }

    /////
}
