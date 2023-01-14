<?php

namespace dnj\Ticket\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
{
    public function toArray($request)
    {
        $this->resource->load('user', 'attachments');

        return parent::toArray($request);
    }
}
