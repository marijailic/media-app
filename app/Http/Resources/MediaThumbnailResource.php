<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin \App\Models\MediaAlbum
 */
class MediaThumbnailResource extends JsonResource
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
            'thumb_url' => $this->getFirstMedia()?->getTemporaryUrl(
                Carbon::now()->addMinutes(5),
                'thumb'
            ),
        ];
    }
}
