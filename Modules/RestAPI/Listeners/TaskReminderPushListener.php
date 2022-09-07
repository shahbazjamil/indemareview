<?php

namespace Modules\RestAPI\Listeners;

use App\Events\TaskReminderEvent;
use Illuminate\Support\Str;

class TaskReminderPushListener extends BasePushNotification
{

    public function handle(TaskReminderEvent $event)
    {
        $task =  $event->task;
        $this->push->setMessage($this->message($task, 'Task Reminder'));

        foreach ($task->users as $user) {
            $this->sendNotification($user);
        }
    }

    private function message($task, $title)
    {
        $type = Str::slug($title, '-');
        return [
            'notification' => [
                'title' => $title . ' #' . $task->id,
                'body' => $task->heading . ($task->project ? ' - Project: ' . $task->project->project_name : ''),
                'sound' => 'default'
            ],
            'data' => [
                'id' => $task->id,
                'type' => $type,
            ]
        ];
    }
}
