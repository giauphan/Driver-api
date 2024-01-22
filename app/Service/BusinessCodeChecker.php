<?php

declare(strict_types=1);

namespace App\Service;

use App\Http\Resources\FileResource;
use App\Models\FileData;
use App\Models\MultiDatabase;
use Illuminate\Support\Facades\Log;

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
            } catch (\Throwable $th) {
                Log::error('Error checking database: '.$th->getMessage());
            } finally {
                MultiMigrationService::disconnectFromMulti();
            }
        }

        return null;

    }
}
