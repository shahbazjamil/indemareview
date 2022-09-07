<?php

namespace App\Http\Controllers\Member;

use App\Helper\Reply;
use App\Product;
use App\Project;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;



// bitsclan code end here

class MemberVisionboardController extends MemberBaseController
{

    // bitsclan code start here

    protected $setting = '';
    protected $envoirment = '';
    protected $quickbook = '';

    // bitsclan code end here

    /**
     * AdminVisionboardController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.visionboard';
        $this->pageIcon = 'icon-newspaper';


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/visionboard'));
        
        //$user = Auth::user();
        $user = Auth::user();
        if (!$user['uuid']) {
            $user['uuid'] = Str::random(15);
            $user->save();
        }
        $uuid = $user['uuid'];
        $this->uuid = $uuid;
        
        return view('member.visionboard.index', $this->data);
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
