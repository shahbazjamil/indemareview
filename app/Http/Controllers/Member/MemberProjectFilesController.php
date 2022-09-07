<?php

namespace App\Http\Controllers\Member;

use App\Helper\Files;
use App\Helper\Reply;
use App\Project;
use App\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\ProjectFolder;
use Illuminate\Support\Facades\DB;

/**
 * Class MemberProjectsController
 * @package App\Http\Controllers\Member
 */
class MemberProjectFilesController extends MemberBaseController
{

    private $mimeType = [
        'txt' => 'fa-file-text',
        'htm' => 'fa-file-code-o',
        'html' => 'fa-file-code-o',
        'php' => 'fa-file-code-o',
        'css' => 'fa-file-code-o',
        'js' => 'fa-file-code-o',
        'json' => 'fa-file-code-o',
        'xml' => 'fa-file-code-o',
        'swf' => 'fa-file-o',
        'flv' => 'fa-file-video-o',

        // images
        'png' => 'fa-file-image-o',
        'jpe' => 'fa-file-image-o',
        'jpeg' => 'fa-file-image-o',
        'jpg' => 'fa-file-image-o',
        'gif' => 'fa-file-image-o',
        'bmp' => 'fa-file-image-o',
        'ico' => 'fa-file-image-o',
        'tiff' => 'fa-file-image-o',
        'tif' => 'fa-file-image-o',
        'svg' => 'fa-file-image-o',
        'svgz' => 'fa-file-image-o',

        // archives
        'zip' => 'fa-file-o',
        'rar' => 'fa-file-o',
        'exe' => 'fa-file-o',
        'msi' => 'fa-file-o',
        'cab' => 'fa-file-o',

        // audio/video
        'mp3' => 'fa-file-audio-o',
        'qt' => 'fa-file-video-o',
        'mov' => 'fa-file-video-o',
        'mp4' => 'fa-file-video-o',
        'mkv' => 'fa-file-video-o',
        'avi' => 'fa-file-video-o',
        'wmv' => 'fa-file-video-o',
        'mpg' => 'fa-file-video-o',
        'mp2' => 'fa-file-video-o',
        'mpeg' => 'fa-file-video-o',
        'mpe' => 'fa-file-video-o',
        'mpv' => 'fa-file-video-o',
        '3gp' => 'fa-file-video-o',
        'm4v' => 'fa-file-video-o',

        // adobe
        'pdf' => 'fa-file-pdf-o',
        'psd' => 'fa-file-image-o',
        'ai' => 'fa-file-o',
        'eps' => 'fa-file-o',
        'ps' => 'fa-file-o',

        // ms office
        'doc' => 'fa-file-text',
        'rtf' => 'fa-file-text',
        'xls' => 'fa-file-excel-o',
        'ppt' => 'fa-file-powerpoint-o',
        'docx' => 'fa-file-text',
        'xlsx' => 'fa-file-excel-o',
        'pptx' => 'fa-file-powerpoint-o',


        // open office
        'odt' => 'fa-file-text',
        'ods' => 'fa-file-text',
    ];

    /**
     * MemberProjectFilesController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = 'app.menu.projects';

        $this->middleware(function ($request, $next) {
            if(!in_array('projects',$this->user->modules)){
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
    public function store(Request $request)
    {
        //echo 'dfddfdf';
        $this->project = Project::with('members', 'members.user')->findOrFail($request->project_id);
        
        $this->folder_id = '';
        if(isset($request->folder_id) && !empty($request->folder_id)) {
            $this->folder_id = $request->folder_id;
        }
        
        if ($request->hasFile('file')) {
            $upload = can_upload($request->file->getSize()/(1000*1024));
            if($upload) {
                $file = new ProjectFile();
                $file->user_id = $this->user->id;
                $file->project_id = $request->project_id;

                $filename =Files::uploadLocalOrS3($request->file,'project-files/'.$request->project_id);

                $file->filename = $request->file->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $request->file->getSize();
                if(isset($request->folder_id) && !empty($request->folder_id)) {
                    $file->folder_id = $request->folder_id;
                }
                $file->save();
                $this->logProjectActivity($request->project_id, __('messages.newFileUploadedToTheProject'));
            } else {
                return Reply::error(__('messages.storageLimitExceedContactAdmin'));
            }
        }

        $this->project = Project::findOrFail($request->project_id);
        $this->icon($this->project);
        
        
        if(!empty($this->folder_id)) {
            $this->files = ProjectFile::where('project_id', $request->project_id)->where('folder_id', $this->folder_id)->get();
        } else {
            $this->files = ProjectFile::where('project_id', $request->project_id)->get();
        }
        
        if(!empty($this->folder_id)) {
            $this->folders = ProjectFolder::where('project_id', $request->project_id)->where('id', $this->folder_id)->get();
        } else {
            $this->folders = ProjectFolder::where('project_id', $request->project_id)->get();
        }
        
        
        if($request->view == 'list') {
            $view = view('member.project-files.ajax-list', $this->data)->render();
        } else {
            $view = view('member.project-files.thumbnail-list', $this->data)->render();
        }
        return Reply::successWithData(__('messages.fileUploaded'), ['html' => $view]);
        //return Reply::dataOnly(['status' => 'success']);
    }

    public function storeMultiple(Request $request)
    {
        echo 'this is ';
        $limitReached = false;
        if ($request->hasFile('file')) {
            foreach ($request->file as $fileData){
                $storage = config('filesystems.default');

                $upload = can_upload($fileData->getSize()/(1000*1024));
                if($upload) {
                    $file = new ProjectFile();
                    $file->user_id = $this->user->id;
                    $file->project_id = $request->project_id;
                    switch($storage) {
                        case 'local':
                            $fileData->image->store('project-files/'.$request->project_id, $fileData->hashName());
                            break;
                        case 's3':
                            Storage::disk('s3')->putFileAs('project-files/'.$request->project_id, $fileData, $fileData->getClientOriginalName(), 'public');
                            break;

                    }

                    $file->filename = $fileData->getClientOriginalName();
                    $file->hashname = $fileData->hashName();
                    $file->size = $fileData->getSize();
                    $file->save();
                    $this->logProjectActivity($request->project_id, __('messages.newFileUploadedToTheProject'));
                } else {
                    $limitReached = true;
                }
            }

        }
        return Reply::redirect(route('admin.projects.index'), __('modules.projects.projectUpdated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->folder_id = '';
        if(isset($request->folder_id) && !empty($request->folder_id)) {
            $this->folder_id = $request->folder_id;
        }
        
        $this->project = Project::findOrFail($id);
        
        if(!empty($this->folder_id)) {
            $this->files = ProjectFile::where('project_id', $id)->where('folder_id', $this->folder_id)->get();
        } else {
            $this->files = ProjectFile::where('project_id', $id)->get();
        }
        
        if(!empty($this->folder_id)) {
            $this->folders = ProjectFolder::where('project_id', $id)->where('id', $this->folder_id)->get();
        } else {
            $this->folders = ProjectFolder::where('project_id', $id)->get();
        }
        
        $this->upload = can_upload();
        return view('member.project-files.show', $this->data);
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
    public function destroy(Request $request, $id)
    {
        $storage = config('filesystems.default');
        $file = ProjectFile::findOrFail($id);
        switch($storage) {
            case 'local':
//                File::delete('user-uploads/project-files/'.$file->project_id.'/'.$file->hashname);
                Files::deleteFile($file->hashname, 'project-files/'.$file->project_id);
                break;
            case 's3':
                Storage::disk('s3')->delete('project-files/'.$file->project_id.'/'.$file->hashname);
                Files::deleteFile($file->hashname, 'project-files/'.$file->project_id);
                break;
        }

        ProjectFile::destroy($id);
        $this->project = Project::findOrFail($file->project_id);
        $this->icon($this->project);
        if($request->view == 'list') {
            $view = view('member.project-files.ajax-list', $this->data)->render();
        } else {
            $view = view('member.project-files.thumbnail-list', $this->data)->render();
        }
        return Reply::successWithData(__('messages.fileDeleted'), ['html' => $view]);
    }
    
    
     public function create_folder(Request $request)
    {
        if(isset($request->folder_name) && !empty($request->folder_name)) {
            
        $folder_password = '';
        if(!is_null($folder_password) && !empty($folder_password)) {
             $folder_password = md5($folder_password);
         }
         $folder = new ProjectFolder();
         $folder->user_id = $this->user->id;
         $folder->project_id = $request->project_id;
         $folder->folder_name = $request->folder_name;
         $folder->save();
         return redirect(route('member.files.show', $request->project_id));
         
//                $path = public_path('filemanager/'.$folder_name);
//                if(!File::isDirectory($path)){
//                    File::makeDirectory($path, 0777, true, true);
//                }
         //return Reply::redirect(route('admin.files.show', $request->project_id), 'Folder created successfully.');
        } else {
            //return Reply::error('Folder name iis required.');
        }
        
        return redirect(route('member.files.show', $request->project_id));
        
        
    }
    
    public function delete_folder($folder_id)
    {
        $folder = DB::table('project_folders')->where('id', $folder_id)->where('company_id', company()->id)->first();
        $project_id = $folder->project_id;
        //$path = public_path('filemanager/'.$folder->folder_name);
        //File::deleteDirectory($path);
        $folder = DB::table('project_folders')->where('id', $folder_id)->where('company_id', company()->id)->delete();
        $folder = DB::table('project_files')->where('folder_id', $folder_id)->where('company_id', company()->id)->delete();
        return Reply::success('Folder Deleted.');
        //return redirect()->route('admin.project-files.show', $project_id);
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id) {
        $file = ProjectFile::findOrFail($id);
        return download_local_s3($file, 'project-files/' . $file->project_id . '/' . $file->hashname);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function thumbnailShow(Request $request)
    {
        $this->folder_id = '';
        if(isset($request->folder_id) && !empty($request->folder_id)) {
            $this->folder_id = $request->folder_id;
        }
        
        $this->project = Project::with('files')->findOrFail($request->id);
        $this->icon($this->project);
        
        if(!empty($this->folder_id)) {
            $this->files = ProjectFile::where('project_id', $request->id)->where('folder_id', $this->folder_id)->get();
        } else {
            $this->files = ProjectFile::where('project_id', $request->id)->get();
        }
        
        if(!empty($this->folder_id)) {
            $this->folders = ProjectFolder::where('project_id', $request->id)->where('id', $this->folder_id)->get();
        } else {
            $this->folders = ProjectFolder::where('project_id', $request->id)->get();
        }
        
        
        $view = view('member.project-files.thumbnail-list', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * @param $projects
     */
    private function icon($projects) {
        foreach ($projects->files as $project) {
            if (is_null($project->external_link)) {
                $ext = pathinfo($project->filename, PATHINFO_EXTENSION);
                if ($ext == 'png' || $ext == 'jpe' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'bmp' ||
                    $ext == 'ico' || $ext == 'tiff' || $ext == 'tif' || $ext == 'svg' || $ext == 'svgz' || $ext == 'psd' || $ext == 'csv')
                {
                    $project->icon = 'images';
                } else {
                    if(isset($this->mimeType[$ext])) {
                       $project->icon = $this->mimeType[$ext];
                    }
                    //if not fund then set doc
                    $project->icon = $this->mimeType['doc'];
                }
            }
        }
    }

}