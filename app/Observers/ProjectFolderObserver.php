<?php

namespace App\Observers;


use App\Notifications\FileUpload;
use App\Project;
use App\ProjectFolder;

class ProjectFolderObserver
{

    public function saving(ProjectFolder $folder)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $folder->company_id = company()->id;
        }
    }

}
