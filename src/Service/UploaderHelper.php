<?php
declare(strict_types=1);

namespace App\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper // This class's only job is to handle the upload of the file and returns the directory of such file to the ProductController
{

    private $uploadsPath;

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadFile(UploadedFile $uploadedFile) // Handles the renaming and moving of a file
    {
        $destination = $this->uploadsPath. '/uploads';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); // gets filename with no extension
        $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension(); // applies a unique identifier to the original filename
        $directory = $destination . '/' . $newFilename;

        $uploadedFile->move(
            $destination,
            $newFilename
        );

        return $directory;
    }


}