<?php

namespace App\Listeners;

use App\Events\DiscussionEvent;
use App\Notifications\NewDiscussion;
use Illuminate\Support\Facades\Notification;

class DiscussionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DiscussionEvent $event
     * @return void
     */
    public function handle(DiscussionEvent $event)
    {
        //Notification::send($event->discussion->project->members_many, new NewDiscussion($event->discussion));
        Notification::send($event->discussion->project->clients_many, new NewDiscussion($event->discussion)); // Added By Sb For clients
        
//         if($event->discussion->project->clients_many) {
//            foreach ($event->discussion->project->clients_many as $client) {
//                Notification::send($client, new NewDiscussion($event->discussion)); 
//            }
//        }
        
        
    }
}
