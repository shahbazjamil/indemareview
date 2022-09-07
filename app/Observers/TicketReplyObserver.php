<?php

namespace App\Observers;

use App\TicketReply;

class TicketReplyObserver
{

    public function saving(TicketReply $ticket)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (auth()->user()->super_admin == 1) {
            $ticket->company_id = 2;
//            if (company()) {
//                $ticket->company_id = company()->id;
//            } else {
//                $ticket->company_id = 2;
//            }
        } else {
            if (company()) {
                $ticket->company_id = company()->id;
            }
        }
    }

    public function created(TicketReply $ticketReply)
    {
//        if (!isRunningInConsoleOrSeeding()) {
//            if (count($ticketReply->ticket->reply) > 1) {
//                if (Auth::user()->hasRole('client') && !is_null($ticketReply->ticket->agent)) {
//                    event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->agent));
//                } else if (Auth::user()->hasRole('client') && is_null($ticketReply->ticket->agent)) {
//                    event(new TicketReplyEvent($ticketReply, null));
//                } else {
//                    event(new TicketReplyEvent($ticketReply, $ticketReply->ticket->client));
//                }
//            }
//        }
    }
}
