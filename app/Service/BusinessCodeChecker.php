<?php
declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\FileResource;
use App\Models\FileData;
use App\Models\MultiDatabase;
use App\Service\MultiMigrationService;

class BusinessCodeChecker
{
    public static function checkExit(string $businessCode)
    {
        $databaseId = 0;
        $file = null;
        $migration = MultiDatabase::get();

        foreach ($migration as $database) {
            MultiMigrationService::switchToMulti($database);
            $file = FileData::where('business_code', $businessCode)->get();

            if (! $file->isEmpty()) {
                $databaseId = $database->id;
                break;
            }

            MultiMigrationService::disconnectFromMulti();
        }

        if ($file == null) {
            return response()->json([
                'status' => 404,
                'error' => 'business code not Found',
            ]);
        }
        $ReFilesearch = new FileResource($file->first(), $databaseId);

        return response()->json([
            'data' => $ReFilesearch->toArray(null),
        ]);
    }
}
