<?php

declare(strict_types=1);

namespace App\Message;

use App\Service\UploaderHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class QueueUploadedFile
{
    /**
     * @var UploaderHelper
     */
    private $uploadedFile;

    public function __construct(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }

    public function getProduct(): UploadedFile
    {
        return $this->uploadedFile;
    }
}
