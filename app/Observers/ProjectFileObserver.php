<?php

namespace App\Observers;


use App\Notifications\FileUpload;
use App\Project;
use App\ProjectFile;
use App\User;
Use App\EmailNotificationSetting;
use Illuminate\Support\Facades\Notification;

class ProjectFileObserver
{

    public function created(ProjectFile $file)
    {
        if (!isRunningInConsoleOrSeeding()) {
                    
            $exceptId = 0;
            $emailSetting = EmailNotificationSetting::where('setting_name', 'When files added')->first();
            if ($emailSetting && isset($emailSetting->send_email) && $emailSetting->send_email == 'no') {
                $user = user();
                if($user) {
                    $exceptId = $user->id;
                }
            }
            
            // send notofctaion to employees 
            $project = Project::with('members', 'members.user')->findOrFail($file->project_id);
            foreach ($project->members as $member) {
                if($exceptId == ! $member->user->id) {
                    $member->user->notify(new FileUpload($file));
                }
            }
            // notofoctaions to clients 
            $clients = $project->clients;
            if ($clients) {
                Notification::send($clients, new FileUpload($file));
            }
            
             // notofoctaions to clients 
//            $clients = User::allClientsWithRole($exceptId);
//            if ($clients) {
//                Notification::send($clients, new FileUpload($file));
//            }
//
//            $employees = User::allEmployees($exceptId);
//            $clients = User::allClientsWithRole($exceptId);
//            
//            if ($employees) {
//                Notification::send($employees, new FileUpload($file));
//            }
//
//            if ($clients) {
//                Notification::send($clients, new FileUpload($file));
//            }
        }
    }

    public function saving(ProjectFile $file)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $file->company_id = company()->id;
        }
    }

}
