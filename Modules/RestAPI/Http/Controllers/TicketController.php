<?php

namespace Modules\RestAPI\Http\Controllers;

use Modules\RestAPI\Entities\Ticket;
use Modules\RestAPI\Entities\TicketReply;
use Modules\RestAPI\Http\Requests\Ticket\IndexRequest;
use Modules\RestAPI\Http\Requests\Ticket\CreateRequest;
use Modules\RestAPI\Http\Requests\Ticket\ShowRequest;
use Modules\RestAPI\Http\Requests\Ticket\UpdateRequest;
use Modules\RestAPI\Http\Requests\Ticket\DeleteRequest;

class TicketController extends ApiBaseController
{

    protected $model = Ticket::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        return $query->visibility();
    }

    public function stored(Ticket $ticket)
    {
        $ticketReply = new TicketReply();
        $ticketReply->ticket_id = $ticket->id;
        $ticketReply->user_id = $ticket->user_id;
        $ticketReply->message = request()->get('description');
        $ticketReply->save();

        return $ticket;
    }
}
