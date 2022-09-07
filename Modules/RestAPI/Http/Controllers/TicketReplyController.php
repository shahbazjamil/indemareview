<?php

namespace Modules\RestAPI\Http\Controllers;

use Modules\RestAPI\Entities\TicketReply;
use Modules\RestAPI\Http\Requests\TicketReply\IndexRequest;
use Modules\RestAPI\Http\Requests\TicketReply\CreateRequest;
use Modules\RestAPI\Http\Requests\TicketReply\ShowRequest;
use Modules\RestAPI\Http\Requests\TicketReply\UpdateRequest;
use Modules\RestAPI\Http\Requests\TicketReply\DeleteRequest;

class TicketReplyController extends ApiBaseController
{

    protected $model = TicketReply::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = CreateRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $showRequest = ShowRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        return $query->visibility();
    }
}
