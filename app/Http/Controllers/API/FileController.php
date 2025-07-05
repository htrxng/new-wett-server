<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryUploader;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $cloudinaryUploader;

    public function __construct(CloudinaryUploader $cloudinaryUploader)
    {
        $this->cloudinaryUploader = $cloudinaryUploader;
    }

    public function store(Request $request): string {
        $photo = null;
        if ($request->hasFile('file')) {
            $photo = $this->cloudinaryUploader->uploadFile($request->file('file'));
        }

        return $photo;
    }
}
