<?php

namespace dnj\Ticket\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        $this->resource->load(['client', 'department']);

        return parent::toArray($request);
    }
}
