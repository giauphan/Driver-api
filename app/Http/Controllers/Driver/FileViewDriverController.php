<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileViewResource;
use App\Models\FileData;
use App\Models\MultiDatabase;
use App\Service\MultiMigrationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class FileViewDriverController extends Controller
{
    public function index(Request $request)
    {
        $filesByDatabase = collect();
        $database_name = MultiDatabase::all();

        $filesByDatabase = $database_name->map(function ($database) {
            return [
                'business_code' => $database->database,
                'has_database_name' => $database->has_database_name,
                'type_data' => 'folder',
                'created_at' => Carbon::parse($database->created_at)->format('--M d.Y'),
            ];
        });

        $perPage = 10; // Set your desired items per page
        $currentPage = request('page', 1);
        $paginatedData = array_slice($filesByDatabase->toArray(), ($currentPage - 1) * $perPage, $perPage);
        $filesByDatabase = new LengthAwarePaginator($paginatedData, count($filesByDatabase), $perPage, $currentPage, ['path' => $request->url()]);

        return view('storage.index', [
            'storages' => $filesByDatabase,
        ]);
    }

    public function show(string $folder, Request $request)
    {

        $validated = Validator::validate($request->all(), [
            'business_code' => ['nullable', 'string'],
        ]);

        $database_name = MultiDatabase::query()
            ->where('has_database_name', $folder)->first();
        if (!$database_name) {
            return response()->json([
                'status' => 404,
                'error' => 'DatabaseID not Found',
            ]);
        }
        MultiMigrationService::switchToMulti($database_name);
        $files = FileData::query()
            ->select('business_code', 'has_business_code', 'type_data')
            ->when(Arr::get($validated, 'business_code'), function (Builder $query, string $business_code) {
                $query->where('business_code', 'like', "%$business_code%");
            })
            ->paginate()
            ->withQueryString();

        MultiMigrationService::disconnectFromMulti();
        $folder = [
            'id' => $database_name->id,
            'has_database_name' => $folder,
        ];

        return view('storage.index', [
            'folder' => $folder,
            'storages' => FileViewResource::collection($files),
        ]);
    }
}
