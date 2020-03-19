<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{

    private $uploadsPath;

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadFile(UploadedFile $uploadedFile)
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