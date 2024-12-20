<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin \App\Models\Media
 */
class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'thumb_url' => $this->getTemporaryUrl(
                Carbon::now()->addMinutes(5),
                'thumb'
            ),
            'full_url' => $this->getTemporaryUrl(
                Carbon::now()->addMinutes(5)
            ),
        ];
    }
}
