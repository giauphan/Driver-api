<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Models\FileData;
use App\Models\MultiDatabase;
use App\Service\MultiMigrationService;
use Illuminate\Http\Request;

class FileViewController extends Controller
{
    public function __invoke(Request $request)
    {
        $limit = $request->input('limit') ?? 1;
        $databaseId = $request->has('DatabaseID') ? (int) $request->input('DatabaseID') : 0;
        if ($request->has('DatabaseID')) {
            $migration = MultiDatabase::find($request->input('DatabaseID'));
            if (! $migration) {
                return response()->json([
                    'status' => 404,
                    'error' => 'DatabaseID not Found',
                ]);
            }

            MultiMigrationService::switchToMulti($migration);
        }
        if ($request->has('business_code')) {
            $file = FileData::query()
                ->where('business_code', $request->input('business_code'))
                ->get();
            if ($file->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'error' => 'business code not Found',
                ]);
            }
        } else {
            $file = FileData::query()
                ->Limit($limit)
                ->get();
        }
        if ($request->has('DatabaseID')) {
            MultiMigrationService::disconnectFromMulti();
        }
        $ReFile = new FileResource($file, $databaseId);

        return response()->json([
            'data' => $ReFile->collection($file),
        ]);
    }
}
