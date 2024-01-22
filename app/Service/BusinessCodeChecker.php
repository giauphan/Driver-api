<?php

declare(strict_types=1);

namespace App\Service;

use App\Http\Resources\FileResource;
use App\Models\FileData;
use App\Models\MultiDatabase;

class BusinessCodeChecker
{
    public static function checkExit(string $businessCode)
    {
        $databaseId = 0;
        $file = null;
        $migration = MultiDatabase::get();

        foreach ($migration as $database) {
            try {
              
            MultiMigrationService::switchToMulti($database);
            $file = FileData::where('business_code', $businessCode)->get();

            if (!$file->isEmpty()) {
                $databaseId = $database->id;
                break;
            }
            MultiMigrationService::disconnectFromMulti();
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if ($file == null) {
            return null;
        }
        $ReFilesearch = new FileResource($file->first(), $databaseId);

        return response()->json([
            'data' => $ReFilesearch->toArray(null),
        ]);
    }
}
