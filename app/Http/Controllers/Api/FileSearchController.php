<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Models\FileData;
use App\Models\MultiDatabase;
use App\Service\MultiMigrationService;
use Illuminate\Http\Request;

class FileSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $databaseId = 0;
        $file = null;
        if ($request->has('business_code')) {
            $migration = MultiDatabase::get();
            foreach ($migration as $database) {
                MultiMigrationService::switchToMulti($database);
                $file = FileData::where('business_code', $request->input('business_code'))->get();

                if (! $file->isEmpty()) {
                    $databaseId = $database->id;
                    break;
                }
                MultiMigrationService::disconnectFromMulti();
            }
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
