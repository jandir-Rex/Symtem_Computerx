<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class ImageHelper
{
    public static function upload(UploadedFile $file, string $folder = 'productos')
    {
        try {
            // Subir a Cloudinary y devolver la URL
            $result = Cloudinary::upload(
                $file->getRealPath(),
                ['folder' => $folder]
            );
            
            return $result->getSecurePath();
        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            throw $e;
        }
    }
}