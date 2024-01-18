<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\FileData
 */
class FileViewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'has_file_name' => $this->has_business_code,
            'business_code' => $this->business_code,
            'Data' => $this->Data,
            'type_data' => $this->type_data,
            'created_at' => Carbon::parse($this->created_at)->format('--M d.Y'),
        ];
    }
}
