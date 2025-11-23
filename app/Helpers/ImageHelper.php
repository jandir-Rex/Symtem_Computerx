<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class ImageHelper
{
    public static function upload(UploadedFile $file, string $folder = 'productos')
    {
        // Subir a Cloudinary y devolver la URL
        $result = Cloudinary::upload(
            $file->getRealPath(),
            ['folder' => $folder]
        );
        
        return $result->getSecurePath();
    }
}