<?php

namespace Modules\RestAPI\Listeners;

use App\Events\TaskEvent;
use Illuminate\Support\Str;

class TaskPushListener extends BasePushNotification
{

    public function handle(TaskEvent $event)
    {
        $task = $event->task;
        // NewClientTask, NewTask, TaskUpdated, TaskCompleted, TaskUpdatedClient
        $title = ucwords(Str::snake($event->notificationName, ' '));
        $this->push->setMessage($this->message($task, $title));

        foreach ($event->notifyUser as $user) {
            $this->sendNotification($user);
        }
    }

    private function message($task, $title)
    {
        $type = Str::slug($title, '-');
        return [
            'notification' => [
                'title' => $title . ' #' . $task->id,
                'body' => $task->heading . ($task->project ? ' - Project:' . $task->project->project_name : ''),
                'sound' => 'default'
            ],
            'data' => [
                'id' => $task->id,
                'type' => $type,
            ]
        ];
    }
}
