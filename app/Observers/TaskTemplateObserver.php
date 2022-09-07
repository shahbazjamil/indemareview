<?php

namespace App\Observers;

use App\TaskTemplate;

class TaskTemplateObserver
{

    public function saving(TaskTemplate $taskTemplate)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $taskTemplate->company_id = company()->id;
        }
    }

}
