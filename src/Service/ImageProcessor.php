<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageProcessor
{
    public const PROFILE_IMAGE_TYPE = 'profile';
    public const POST_IMAGE_TYPE = 'post';
    public const PHOTO_IMAGE_TYPE = 'photo';

    private const MAX_PROFILE_PIC_WIDTH = 200;
    private const MAX_PROFILE_PIC_HEIGHT = 200;

    private const MAX_POST_PIC_WIDTH = 1200;
    private const MAX_POST_PIC_HEIGHT = 1200;

    private const MAX_PHOTO_WIDTH = 1200;
    private const MAX_PHOTO_HEIGHT = 1200;

    private KernelInterface $kernel;
    private Imagine $imagine;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->imagine = new Imagine();
    }

    public function saveImage(UploadedFile $image, string $imageType, string $storagePath = '/public/images/')
    {
        //TODO: Resize image
        $newFileName = uniqid() . '.' . $image->guessExtension();
        try {
            switch ($imageType) {
                case self::PROFILE_IMAGE_TYPE:
                    $width = self::MAX_PROFILE_PIC_WIDTH;
                    $height = self::MAX_PROFILE_PIC_HEIGHT;
                    break;
                case self::POST_IMAGE_TYPE:
                    $width = self::MAX_POST_PIC_WIDTH;
                    $height = self::MAX_POST_PIC_HEIGHT;
                    break;
                default:
                    $width = self::MAX_PHOTO_WIDTH;
                    $height = self::MAX_PHOTO_HEIGHT;
                    break;
            }

            $image->move($this->kernel->getProjectDir() . $storagePath, $newFileName);
            $this->imagine
                ->open($this->kernel->getProjectDir() . $storagePath . $newFileName)
                ->resize(new Box($width, $height))
                ->save();

            return $newFileName;
        } catch (FileException $e) {
            return new Response($e->getMessage());
        }
    }
}