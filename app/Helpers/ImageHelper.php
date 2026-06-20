<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Convert an uploaded image file into WebP format and save it on the specified disk/folder.
     * Falls back to normal upload for SVGs or if conversion fails.
     */
    public static function storeAsWebp(UploadedFile $file, string $folder, string $disk = 'public', int $quality = 85): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        // Fallback for SVGs or files that don't need/support conversion
        if ($extension === 'svg' || $mime === 'image/svg+xml') {
            return $file->store($folder, $disk);
        }

        // Try converting using GD
        try {
            $realPath = $file->getRealPath();
            $imageContent = file_get_contents($realPath);
            if ($imageContent === false) {
                return $file->store($folder, $disk);
            }

            $image = @imagecreatefromstring($imageContent);
            if ($image === false) {
                // GD could not create image, fall back to standard store
                return $file->store($folder, $disk);
            }

            // Ensure transparency and truecolor are preserved
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);

            // Resize image to maximum 800px boundary if larger (maintains original ratio)
            $width = imagesx($image);
            $height = imagesy($image);
            $maxSize = 800;

            if ($width > $maxSize || $height > $maxSize) {
                if ($width > $height) {
                    $newWidth = $maxSize;
                    $newHeight = (int) ($height * ($maxSize / $width));
                } else {
                    $newHeight = $maxSize;
                    $newWidth = (int) ($width * ($maxSize / $height));
                }

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);

                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resizedImage;
            }

            // Create a temp file path
            $tempFile = tempnam(sys_get_temp_dir(), 'webp');
            if ($tempFile === false) {
                imagedestroy($image);

                return $file->store($folder, $disk);
            }

            // Write webp data to the temp file
            $success = @imagewebp($image, $tempFile, $quality);
            imagedestroy($image);

            if (! $success) {
                @unlink($tempFile);

                return $file->store($folder, $disk);
            }

            // Store the converted webp file using Laravel Storage
            $fileName = Str::random(40).'.webp';
            Storage::disk($disk)->putFileAs($folder, new File($tempFile), $fileName);

            // Clean up temp file
            @unlink($tempFile);

            return $folder.'/'.$fileName;
        } catch (\Throwable $e) {
            // Log warning or just fall back to standard store
            \Log::warning('WebP conversion failed, falling back to original upload: '.$e->getMessage());

            return $file->store($folder, $disk);
        }
    }
}
