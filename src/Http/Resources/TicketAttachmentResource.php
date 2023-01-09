<?php

namespace dnj\Ticket\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketAttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
