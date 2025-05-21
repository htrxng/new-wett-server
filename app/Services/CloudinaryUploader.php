<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryUploader
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);
    }

    public function uploadFiles(array $files): array
    {
        $urls = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = $this->cloudinary->uploadApi()->upload($file->getRealPath());
                $urls[] = $result['secure_url'];
            }
        }
        return $urls;
    }

    public function uploadFile(UploadedFile $file): ?string
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath());
        return $result['secure_url'];
    }
}
