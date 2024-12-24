<?php

namespace App\Http\Controllers\Api\keeper;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToRetrieveMetadata;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
//        $this->middleware(['permission:Keeper']);

    }
    public function index()
    {
        // Retrieve the warehouse for the currently authenticated user
        $keeper = Warehouse::where('user_id', Auth::user()->id)->first();

        // Check if the warehouse exists
        if (!$keeper) {
            return response()->json(['message' => __('Warehouse not found.')], 404);
        }

        // Convert the warehouse name to a slug-friendly format
        $warehouseName = Str::slug($keeper->name);

        // Define the search pattern for file names
        $pattern = 'inventory_' . $warehouseName . '_*.xlsx';

        // Get all files that match the pattern
        $files = Storage::disk('public')->allFiles('keeper');
        $matchedFiles = array_filter($files, function ($file) use ($pattern) {
            return fnmatch($pattern, basename($file));
        });

        $fileList = array_map(function ($file) {
            try {
                return [
                    'path' =>  Storage::disk('public')->url($file),
                    'name' => basename($file),
                    'size' => $this->formatSize(Storage::disk('public')->size($file)),
                    'last_modified' => Carbon::createFromTimestamp(Storage::disk('public')->lastModified($file))->format('Y-m-d H:i:s'),
                ];
            } catch (UnableToRetrieveMetadata $e) {
                // Handle the error gracefully
                return [
                    'path' => $file,
                    'name' => basename($file),
                    'size' => 'Unable to retrieve',
                    'last_modified' => 'Unable to retrieve',
                    'error' => $e->getMessage(),
                ];
            }
        }, $matchedFiles);
        return response()->json($fileList);
    }

    /**
     * Format file size to human-readable format.
     *
     * @param int $bytes
     * @return string
     */
    private function formatSize(int $bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }
}
