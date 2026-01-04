<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CloudinaryService
{
    /**
     * Upload an image to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder Folder path in Cloudinary (e.g., 'events/posters')
     * @param array $options Additional upload options
     * @return array ['url' => string, 'public_id' => string, 'secure_url' => string]
     */
    public function uploadImage(UploadedFile $file, string $folder = 'uploads', array $options = []): array
    {
        $defaultOptions = [
            'folder' => $folder,
            'resource_type' => 'image',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ];

        $uploadOptions = array_merge($defaultOptions, $options);

        // Use getPathname() instead of getRealPath() for better compatibility
        $filePath = $file->getPathname() ?: $file->getRealPath();
        
        if (empty($filePath)) {
            throw new \Exception('Could not get file path from uploaded file');
        }

        $uploadedFile = Cloudinary::upload(
            $filePath,
            $uploadOptions
        );

        return [
            'url' => $uploadedFile->getSecurePath(),
            'public_id' => $uploadedFile->getPublicId(),
            'secure_url' => $uploadedFile->getSecurePath(),
            'original_filename' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Upload a PDF to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadPdf(UploadedFile $file, string $folder = 'pdfs', array $options = []): array
    {
        $defaultOptions = [
            'folder' => $folder,
            'resource_type' => 'raw', // For non-image files like PDFs
        ];

        $uploadOptions = array_merge($defaultOptions, $options);

        // Use getPathname() instead of getRealPath() for better compatibility
        $filePath = $file->getPathname() ?: $file->getRealPath();
        
        if (empty($filePath)) {
            throw new \Exception('Could not get file path from uploaded file');
        }

        $uploadedFile = Cloudinary::upload(
            $filePath,
            $uploadOptions
        );

        return [
            'url' => $uploadedFile->getSecurePath(),
            'public_id' => $uploadedFile->getPublicId(),
            'secure_url' => $uploadedFile->getSecurePath(),
            'original_filename' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Upload any file to Cloudinary (images, PDFs, Word docs, etc.)
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $resourceType 'image', 'raw', or 'auto'
     * @param array $options
     * @return array
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', string $resourceType = 'auto', array $options = []): array
    {
        $defaultOptions = [
            'folder' => $folder,
            'resource_type' => $resourceType,
        ];

        $uploadOptions = array_merge($defaultOptions, $options);

        $filePath = $file->getPathname() ?: $file->getRealPath();
        
        if (empty($filePath)) {
            throw new \Exception('Could not get file path from uploaded file');
        }

        $uploadedFile = Cloudinary::upload(
            $filePath,
            $uploadOptions
        );

        return [
            'url' => $uploadedFile->getSecurePath(),
            'public_id' => $uploadedFile->getPublicId(),
            'secure_url' => $uploadedFile->getSecurePath(),
            'original_filename' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Upload QR code image to Cloudinary
     *
     * @param string $qrCodePath Path to QR code file
     * @param string $filename Desired filename
     * @param string $folder
     * @return array
     */
    public function uploadQrCode(string $qrCodePath, string $filename, string $folder = 'qr-codes'): array
    {
        $uploadedFile = Cloudinary::upload(
            $qrCodePath,
            [
                'folder' => $folder,
                'public_id' => pathinfo($filename, PATHINFO_FILENAME),
                'resource_type' => 'image',
            ]
        );

        return [
            'url' => $uploadedFile->getSecurePath(),
            'public_id' => $uploadedFile->getPublicId(),
            'secure_url' => $uploadedFile->getSecurePath(),
        ];
    }

    /**
     * Delete a file from Cloudinary
     *
     * @param string $publicId
     * @param string $resourceType ('image', 'raw', 'video')
     * @return bool
     */
    public function delete(string $publicId, string $resourceType = 'image'): bool
    {
        try {
            $result = Cloudinary::destroy($publicId, [
                'resource_type' => $resourceType
            ]);
            
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            \Log::error('Cloudinary deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get optimized image URL with transformations
     *
     * @param string $publicId
     * @param array $transformations
     * @return string
     */
    public function getImageUrl(string $publicId, array $transformations = []): string
    {
        return Cloudinary::getUrl($publicId, $transformations);
    }

    /**
     * Generate a thumbnail URL
     *
     * @param string $publicId
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getThumbnail(string $publicId, int $width = 300, int $height = 300): string
    {
        return $this->getImageUrl($publicId, [
            'width' => $width,
            'height' => $height,
            'crop' => 'fill',
            'gravity' => 'auto',
            'quality' => 'auto',
        ]);
    }

    /**
     * Upload multiple files
     *
     * @param array $files Array of UploadedFile instances
     * @param string $folder
     * @param string $type 'image', 'pdf', or 'raw'
     * @return array Array of upload results
     */
    public function uploadMultiple(array $files, string $folder = 'uploads', string $type = 'image'): array
    {
        $results = [];

        foreach ($files as $file) {
            if ($type === 'pdf') {
                $results[] = $this->uploadPdf($file, $folder);
            } else {
                $results[] = $this->uploadImage($file, $folder);
            }
        }

        return $results;
    }
}
