<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'body'       => $this->body,
            'screen'     => $this->screen,
            'data'       => $this->data,
            'type'       => $this->type,
            'is_read'    => (bool) $this->is_read,
            // Jordan local time with no "Z": the app parses offset-less strings as
            // device-local time, so it displays the real time (a UTC "…Z" value
            // was shown unconverted, i.e. hours off).
            'read_at'    => $this->read_at?->copy()->timezone('Asia/Amman')->format('Y-m-d\TH:i:s'),
            'created_at' => $this->created_at?->copy()->timezone('Asia/Amman')->format('Y-m-d\TH:i:s'),
        ];
    }
}
