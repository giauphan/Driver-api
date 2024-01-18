<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\FileData
 */
class FileResource extends JsonResource
{
    public int $DatabaseId;

    public function __construct($resource, int $DatabaseId)
    {
        parent::__construct($resource);
        $this->DatabaseId = $DatabaseId;
    }

    public function toArray($request = null): array
    {
        if ($this->resource === null) {
            return [];
        }
        $request = $request ?? app('request');

        return [
            'id' => $this->id,
            'business_code' => $this->business_code,
            'has_business_code' => $this->has_business_code,
            'url_preview' => $this->DatabaseId != 0
                ? route('preview', ['id' => $this->has_business_code, 'DatabaseID' => $this->DatabaseId])
                : route('preview', ['id' => $this->has_business_code]),
        ];
    }
}
