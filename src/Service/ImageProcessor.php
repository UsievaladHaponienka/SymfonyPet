<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageProcessor
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function saveImage(UploadedFile $image, string $storagePath = '/public/images')
    {
        //TODO: Resize image
        $newFileName = uniqid() . '.' . $image->guessExtension();
        try {
            $image->move($this->kernel->getProjectDir() . $storagePath, $newFileName);

            return $newFileName;
        } catch (FileException $e) {
            return new Response($e->getMessage());
        }

    }

}