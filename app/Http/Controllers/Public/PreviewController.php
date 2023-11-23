<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FileData;
use App\Models\MultiDatabase;
use App\Service\MultiMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PreviewController extends Controller
{
    public function index(Request $request)
    {

        if ($request->has('id')) {
            $id = $request->input('id');
            $file = $this->findFileById($request, $id);

            if ($file) {
                $decodedData = base64_decode($file->DataBcdn);
                $filename = $this->getFilename($file);
                $response = Response::make($decodedData, 200);
                $response->header('Content-Disposition', "attachment; filename=$filename.$file->type_data");
                $response->header('Content-Type', 'application/pdf');

                return $response;
            }

            return abort(404);
        }
    }

    private function findFileById(Request $request, $id)
    {
        $fileQuery = FileData::query()->where('has_business_code', 'like', "%$id%");

        if ($request->has('DatabaseID')) {
            $migration = MultiDatabase::findOrFail($request->input('DatabaseID'));
            MultiMigrationService::switchToMulti($migration);
            $file = $fileQuery->firstOrFail();
            MultiMigrationService::disconnectFromMulti();
        } else {
            $file = $fileQuery->firstOrFail();
        }

        return $file;
    }

    private function getFilename($file)
    {
        return ! empty($file->business_code) ? $file->business_code : $file->has_business_code;
    }
}
