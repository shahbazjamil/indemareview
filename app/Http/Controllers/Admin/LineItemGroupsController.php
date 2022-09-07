<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\LineItemGroup\StoreLineItemGroup;
use App\LineItemGroup;

class LineItemGroupsController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
    }

    public function create()
    {
        $this->groups = LineItemGroup::all();
        return view('admin.line_item_groups.create', $this->data);
    }

    public function store(StoreLineItemGroup $request)
    {
        $group = new LineItemGroup();
        $group->group_name = $request->group_name;
        $group->group_desc = $request->group_desc;
        $group->save();
        
        return Reply::success('Group Added');
    }
}
