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
            $file = FileData::where('business_code', $businessCode)->first();

            if ($file != null) {
                $databaseId = $database->id;
                $ReFilesearch = new FileResource($file, $databaseId);

                return response()->json([
                    'data' => $ReFilesearch->toArray(null),
                ]);
            }

            MultiMigrationService::disconnectFromMulti();
            } catch (\Throwable $th) {
         
            }
        }

        if ($file == null) {
            return null;
        }
       
    }
}
