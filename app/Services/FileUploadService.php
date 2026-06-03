<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Store a file in the specified directory using the public disk.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    /**
     * Replace an existing file with a new one.
     *
     * @param string|null $oldPath
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function replace(?string $oldPath, UploadedFile $file, string $directory): string
    {
        if ($oldPath) {
            $this->delete($oldPath);
        }
        return $this->store($file, $directory);
    }

    /**
     * Delete a file from the public disk if it exists.
     *
     * @param string|null $path
     * @return void
     */
    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
