<?php

namespace App\Support;

use Cloudinary\Cloudinary;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToDeleteFile;

class CloudinaryAdapter implements FilesystemAdapter
{
    protected $cloudinary;
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->cloudinary = new Cloudinary($config['url'] ?? $config);
    }

    public function fileExists(string $path): bool
    {
        try {
            $this->cloudinary->adminApi()->asset($this->getPublicId($path));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        return true; // Cloudinary doesn't have real directories
    }

    public function write(string $path, string $contents, Config $config): void
    {
        try {
            $tempPath = tempnam(sys_get_temp_dir(), 'cloudinary_');
            file_put_contents($tempPath, $contents);
            
            $result = $this->cloudinary->uploadApi()->upload($tempPath, [
                'public_id' => $this->getPublicId($path),
                'resource_type' => $this->getResourceType($path),
                'folder' => $this->getFolder($path),
            ]);
            
            unlink($tempPath);
        } catch (\Exception $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        try {
            $url = $this->cloudinary->image($this->getPublicId($path))->toUrl();
            return file_get_contents($url);
        } catch (\Exception $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    public function readStream(string $path)
    {
        try {
            $url = $this->cloudinary->image($this->getPublicId($path))->toUrl();
            $stream = fopen($url, 'r');
            return $stream;
        } catch (\Exception $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    public function delete(string $path): void
    {
        try {
            $this->cloudinary->uploadApi()->destroy($this->getPublicId($path), [
                'resource_type' => $this->getResourceType($path)
            ]);
        } catch (\Exception $e) {
            throw UnableToDeleteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function deleteDirectory(string $path): void
    {
        // Cloudinary doesn't have real directories
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Cloudinary doesn't need directory creation
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // Cloudinary handles visibility differently
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($path, null, null, null, mime_content_type($path));
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path, null, null, time());
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path, 0);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return [];
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->copy($source, $destination, $config);
        $this->delete($source);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $contents = $this->read($source);
        $this->write($destination, $contents, $config);
    }

    public function getUrl(string $path): string
    {
        return $this->cloudinary->image($this->getPublicId($path))->toUrl();
    }

    protected function getPublicId(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    protected function getFolder(string $path): string
    {
        $dir = dirname($path);
        return $dir === '.' ? '' : $dir;
    }

    protected function getResourceType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'])) {
            return 'image';
        }
        
        if (in_array($extension, ['mp4', 'avi', 'mov', 'wmv'])) {
            return 'video';
        }
        
        return 'raw'; // PDF, documents, etc.
    }
}
