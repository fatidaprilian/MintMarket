<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file_upload' => 'required|file|max:10240',
        ]);

        $file = $request->file('file_upload');

        $token = env('VERCEL_BLOB_TOKEN');
        $baseUrl = env('VERCEL_BLOB_URL');

        if (!$token || !$baseUrl) {
            return response()->json(['message' => 'VERCEL_BLOB_TOKEN atau VERCEL_BLOB_URL belum diatur di .env'], 500);
        }

        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension = $file->getClientOriginalExtension();
        $uniqueName = Str::slug($fileName) . '-' . uniqid() . '.' . $fileExtension;
        $filePath = 'products/' . $uniqueName;

        $url = rtrim($baseUrl, '/') . '/' . $filePath;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => $file->getMimeType(),
        ])->put(
            $url,
            file_get_contents($file->getRealPath())
        );

        if ($response->successful()) {
            return response()->json([
                'message' => 'File berhasil di-upload!',
                'url' => $url,
                'pathname' => $filePath,
            ]);
        } else {
            return response()->json([
                'message' => 'Upload ke Vercel Blob gagal.',
                'error' => $response->body()
            ], $response->status());
        }
    }
}
