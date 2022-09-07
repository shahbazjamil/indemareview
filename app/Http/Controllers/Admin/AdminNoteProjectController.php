<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\Tasks\StoreTaskNote;
use App\ProjectNote;
use Illuminate\Http\Request;

class AdminNoteProjectController extends AdminBaseController
{
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
    public function store(StoreTaskNote $request)
    {
        $note = new ProjectNote();
        $note->note = $request->note;
        $note->project_id = $request->projectId;
        $note->user_id = $this->user->id;
        $note->save();

        $this->notes = ProjectNote::where('project_id', $request->projectId)->orderBy('id', 'desc')->get();
        $view = view('admin.projects.project_note', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $note = ProjectNote::findOrFail($id);
        $note_project_id = $note->project_id;
        $note->delete();
        $this->notes = ProjectNote::where('project_id', $note_project_id)->orderBy('id', 'desc')->get();
        $view = view('admin.projects.project_note', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
}
