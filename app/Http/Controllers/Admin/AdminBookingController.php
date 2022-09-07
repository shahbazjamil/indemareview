<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Product;
use App\Project;
use App\User;




// bitsclan code end here

class AdminBookingController extends AdminBaseController
{

    // bitsclan code start here

    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';

    // bitsclan code end here

    /**
     * AdminBookingController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.bookings';
        $this->pageIcon = 'icon-book-open';

        

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //$user = Auth::user();
        
        return view('admin.bookings.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
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
        //
    }
}
