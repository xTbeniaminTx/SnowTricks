<?php


namespace App\Service;


use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const TRICK_IMAGE = 'trick_image';

    private $privateFilename;

    public function __construct(FilesystemInterface $privateUploadedFilename)
    {
        $this->privateFilename = $privateUploadedFilename;
    }

    public function uploadTrickImage(File $file): string
    {
        return $this->uploadFile($file, self::TRICK_IMAGE);
    }

    private function uploadFile(File $file, string $directory): string
    {
        if ($file instanceof UploadedFile) {
            $originalFileWhitoutExt = $file->getClientOriginalName();
        } else {
            $originalFileWhitoutExt = $file->getFilename();
        }

        $newFileName = pathinfo($originalFileWhitoutExt, PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');
        $result = $this->privateFilename->writeStream(
            $directory . '/' . $newFileName,
            $stream
        );

        if ($result === false) {
            throw new \Exception(sprintf('Imposible de ecrire le fichier uploade "%s"', $newFileName));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFileName;
    }

}