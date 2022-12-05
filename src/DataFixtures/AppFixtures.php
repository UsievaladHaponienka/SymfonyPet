<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Profile;
use App\Entity\User;
use App\Service\ImageProcessor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private ImageProcessor $imageProcessor;
    private KernelInterface $kernel;

    private const DEFAULT_USER_PASSWORD = 'test@123';

    private array $userData = [
        [
            'email' => 'metal_fan@gmail.com',
            'username' => 'Metal Fan',
            'profile_description' => 'Huge fan of metal \\m/',
            'profile_image' => 'profile_1'
        ],
        [
            'email' => 'post_rock_fan@gmail.com',
            'username' => 'Post-Rock Fan',
            'profile_description' => 'Huge fan of Post-Rock',
            'profile_image' => 'profile_2'
        ],
    ];

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager,
        ImageProcessor              $imageProcessor,
        KernelInterface             $kernel,
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->imageProcessor = $imageProcessor;
        $this->kernel = $kernel;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsers();

        $manager->flush();
    }

    protected function getFixturesImagesDir(): string
    {
        return $this->kernel->getProjectDir() . '/fixtures/images/';
    }

    protected function createImage(string $filename): UploadedFile
    {
        copy($this->getFixturesImagesDir() . $filename . '.jpg',
            $this->getFixturesImagesDir() . $filename . '_copy.jpg'
        );

        return new UploadedFile(
            $this->getFixturesImagesDir() . $filename . '_copy.jpg',
            $filename . '_copy.jpg',
            null,
            null,
            true
        );
    }

    protected function createUsers()
    {
        foreach ($this->userData as $userDatum) {
            $user = new User();
            $user->setEmail($userDatum['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, self::DEFAULT_USER_PASSWORD));

            $profile = new Profile();
            $user->setProfile($profile);
            $this->entityManager->persist($user);

            $profile->setUsername($userDatum['username']);
            $profile->setDescription($userDatum['profile_description']);
            $profileImage = $this->createImage($userDatum['profile_image']);
            $newProfileImageName = $this->imageProcessor->saveImage(
                $profileImage,
                ImageProcessor::PROFILE_IMAGE_TYPE,
                '/public/images/profile/'
            );

            $profile->setProfileImageUrl('/images/profile/' . $newProfileImageName);
            $this->entityManager->persist($profile);

            $defaultUserAlbum = new Album();
            $defaultUserAlbum->setProfile($profile);
            $defaultUserAlbum->setType(Album::USER_DEFAULT_TYPE);
            $defaultUserAlbum->setTitle(Album::DEFAULT_ALBUM_TITLE);

            $this->entityManager->persist($defaultUserAlbum);
        }
    }
}
