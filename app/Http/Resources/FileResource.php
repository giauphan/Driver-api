<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\FileData
 */
class FileResource extends JsonResource
{
    public static $DatabaseId;

    public function __construct($resource, int $DatabaseId)
    {
        parent::__construct($resource);
        self::$DatabaseId = $DatabaseId;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_code' => $this->business_code,
            'has_business_code' => $this->has_business_code,
            'url_preview' => self::$DatabaseId != 0 ? route('preview', ['id' => $this->has_business_code, 'DatabaseID' => self::$DatabaseId]) : route('preview', ['id' => $this->has_business_code]),
        ];
    }
}
