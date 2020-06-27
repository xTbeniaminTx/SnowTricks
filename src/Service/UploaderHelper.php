<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    /**
     * @var string
     */
    private $pathUpload;

    public function __construct(string $pathUpload)
    {
        $this->pathUpload = $pathUpload;
    }

    public function uploadTrickImage(File $file): string
    {
        return $this->uploadFile($file);
    }

    private function uploadFile(File $file): ?string
    {
        if (!$file instanceof UploadedFile) {
            return null;
        }

        $newFileName = pathinfo( $file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->pathUpload, $newFileName);

        return $newFileName;
    }

}