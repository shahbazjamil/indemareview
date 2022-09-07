<?php

namespace App\Http\Controllers\Member;

use App\Helper\Reply;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Filemanager;

class FileManagerController extends MemberBaseController
{
    protected $file_manager = 'filemanager';
    protected $folder_files = 'folder_files';

    public function index()
    {

        $this->mixPanelTrackEvent('view_page', array('page_path' => '/member/show-file-manager'));
        
        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'File Manager';

        $folders = DB::table($this->file_manager)->where('company_id', company()->id)->get();

        $files = 0;

        foreach ($folders as $folder) {
            $files += $folder->files;
        }

        $this->data['folders'] = $folders;
        $this->data['files'] = $files;

        return view('member.file-manager.index',$this->data);
    }

//    public function view_filemanager()
//    {
//
//        $this->pageIcon = 'icon-settings';
//        $this->pageTitle = 'File Manager';
//
//        return view('file-manager.index', $this->data);
//
//    }

    public function create_folder()
    {
        if ($_POST) {
            $post = $_POST;

            if (array_get($post, 'folder_name', '') != '') {
                $folder_name = array_get($post, 'folder_name', '');
                 $folder_password = array_get($post, 'folder_password', '');
                  if(!is_null($folder_password) && !empty($folder_password)) {
                    $folder_password = md5($folder_password);
                }

                DB::table('filemanager')->insert([
                    'company_id' =>  company()->id,
                    'folder_name'=>$folder_name,
                    'folder_password'=>$folder_password,
                    'created_by'=>Auth::user()->email,
                    'created_date'=>Carbon::now(),
                ]);

                $path = public_path('filemanager/'.$folder_name);

                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }
            }
        }

        return redirect()->route('member.view-file-manager');
    }

    public function preview_folder($folder_id)
    {

        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'File Manager';

        $folder = DB::table($this->file_manager)->where('id', $folder_id)->where('company_id', company()->id)->get();
        $files = DB::table($this->folder_files)->where('folder_id', $folder_id)->where('company_id', company()->id)->get();

        $folder = $folder['0'];
        
         if(!is_null($folder->folder_password) && !empty($folder->folder_password) && !session()->get('folder_id_ss_'.$folder->id)) {
             session()->put('error', 'You do not have permission to view this folder.');
            return redirect()->route('member.view-file-manager');
        }
        
        
//        $path = public_path('filemanager/'.$folder->folder_name);
//        $all_files = File::allFiles($path);

        $this->data['files'] = $files;
        $this->data['folder_name'] = $folder->folder_name;
        $this->data['folder_id'] = $folder->id;
        $this->data['host'] = request()->getHttpHost();

        return view('member.file-manager.folder',$this->data);
    }

    public function add_file($folder_id)
    {

        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'File Manager';

        $folder = DB::table($this->file_manager)->where('id', $folder_id)->where('company_id', company()->id)->first();

        $this->upload = can_upload();
        $this->data['folder_id'] = $folder_id;
        $this->data['folder_name'] = $folder->folder_name;

        return view('member.file-manager.add_file',$this->data);
    }

    public function delete_folder($folder_id)
    {
        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'File Manager';

        $folder = DB::table($this->file_manager)->where('id', $folder_id)->where('company_id', company()->id)->first();

        $path = public_path('filemanager/'.$folder->folder_name);

        File::deleteDirectory($path);

        $folder = DB::table($this->file_manager)->where('id', $folder_id)->where('company_id', company()->id)->delete();
        $folder = DB::table($this->folder_files)->where('folder_id', $folder_id)->where('company_id', company()->id)->delete();

        return redirect()->route('member.view-file-manager');
    }

    public function delete_file($folder_id, $file_id)
    {
        $this->pageIcon = 'icon-settings';
        $this->pageTitle = 'File Manager';

        $folder = DB::table($this->file_manager)->where('id', $folder_id)->where('company_id', company()->id)->first();
        $file = DB::table($this->folder_files)->where('id', $file_id)->where('company_id', company()->id)->first();

        $path = public_path('filemanager/'.$folder->folder_name.'/'.$file->file_name);

        File::delete($path);

        $folder = DB::table($this->folder_files)->where('id', $file_id)->where('company_id', company()->id)->delete();

        return redirect()->route('member.preview_folder', ['folder_id'=>$folder_id]);
    }

    public function storeFile(Request $request)
    {
        if ($_POST) {
            $up_docs = array_get($_POST, 'up_docs', '');
            $folder_id = array_get($_POST, 'folder_id', '');

            $files = explode(":|:", $up_docs);

            foreach ($files as $file) {
                DB::table($this->folder_files)->insert([
                    'company_id' =>  company()->id,
                    'folder_id'=>$folder_id,
                    'file_name'=>$file,
                    'created_by'=>Auth::user()->email,
                    'created_date'=>Carbon::now(),
                ]);
            }
        }

        if (!empty($folder_id)) {
            return redirect()->route('member.preview_folder', ['folder_id'=>$folder_id]);
        } else {
            return redirect()->route('member.view-file-manager');
        }

    }
    
    public function checkPassword(Request $request)
    {
        if(isset($request->folder_id) && !is_null($request->folder_id) &&  isset($request->folder_password) && !is_null($request->folder_password)) {
            $folder_password = md5($request->folder_password);
            $folder = DB::table($this->file_manager)->where('id', $request->folder_id)->where('company_id', company()->id)->first();
            if($folder && $folder->folder_password == $folder_password) {
                session()->put('folder_id_ss_'.$folder->id , $folder->id);
                return Reply::redirect(route('member.preview_folder', ['folder_id'=>$folder->id], 'The password matched.'));
            }
        }
        return Reply::error('The password do not match our records.', 'folder_password');
    }
    
    public function changePassword(Request $request)
    {
        if (isset($request->folder_idd) && !is_null($request->folder_idd) && isset($request->folder_passwordd) && !is_null($request->folder_passwordd)) {
            $folder = Filemanager::find($request->folder_idd);
            if ($folder) {
                $folder->folder_password = md5($request->folder_passwordd);
                $folder->save();
                return Reply::success('Password has been changed.');
            }
        }
    }

}
