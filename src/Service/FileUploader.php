<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $uploadDirectory;

    public function __construct($uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }

    public function upload(UploadedFile $file, string $folder)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getUploadDirectory().'/'.$folder, $fileName);
        } catch (FileException $e) {
            throw FileException('Erreur in file process');
        }

        return $fileName;
    }

    public function getUploadDirectory()
    {
        return $this->uploadDirectory;
    }
}