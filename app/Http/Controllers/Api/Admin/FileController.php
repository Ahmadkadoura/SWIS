<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;

class FileController extends Controller
{
    public function __construct( )
    {
        $this->middleware(['auth:sanctum']);
//        $this->middleware(['permission:Admin']);
//        $this->middleware(['permission:Keeper'])->only(['downloadFile']);

    }
    public function index()
    {
        // Get all files in the storage/public/exports directory
        $files = Storage::disk('public')->allFiles('exports');

        // Organize the file paths or add more details
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
        }, $files);

        // Return the list of files as a JSON response
        return response()->json($fileList);
    }

    public function downloadFile(Request $request)
    {
        // Extract the file path from the provided URL
        $url = $request->input('url');  // The full URL to the file
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = url($url);  // Convert relative path to full URL
        }

        // Extract the relative file path by removing the base URL
        $filePath = str_replace('/storage/', 'public/', parse_url($url, PHP_URL_PATH));
        // Ensure the file exists in the public storage disk
        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }

        return response()->json([
            'message' => __('File not found!')
        ], 404);
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
