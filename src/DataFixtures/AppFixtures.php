<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Group;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use DateTimeImmutable;
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
        [
            'email' => 'led_zeppelin_fan@gmail.com',
            'username' => 'Classic Rock Fan',
            'profile_description' => 'Huge fan of Classic Rock, especially Led Zeppelin',
            'profile_image' => 'profile_3'
        ],
    ];

    private array $groupData = [
        [
            'title' => 'Metallica Fan Club',
            'type' => 'public',
            'description' => 'Group which unites all fans of Metallica, USA Thrash Metal Band',
            'admin_username' => 'Metal Fan',
            'group_image' => 'group_1',
            'members_usernames' => ['Post-Rock Fan', 'Classic Rock Fan']
        ],
        [
            'title' => 'Slayer Fan Club',
            'type' => 'public',
            'description' => 'Group which unites all fans of Slayer, USA Thrash Metal Band',
            'admin_username' => 'Metal Fan',
            'group_image' => 'group_2',
            'members_usernames' => []
        ],
        [
            'title' => 'God Is An Astronaut Fan Club',
            'type' => 'private',
            'description' => 'Group which unites all fans of God Is An Astronaut, Ireland Post Rock Band',
            'admin_username' => 'Post-Rock Fan',
            'group_image' => 'group_3',
            'members_usernames' => ['Classic Rock Fan']
        ]
    ];
    private ProfileRepository $profileRepository;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager,
        ImageProcessor              $imageProcessor,
        KernelInterface             $kernel,
        ProfileRepository           $profileRepository
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->imageProcessor = $imageProcessor;
        $this->kernel = $kernel;
        $this->profileRepository = $profileRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsers();
        $this->createGroups();

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

    protected function createUsers(): void
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

        $this->entityManager->flush();
    }

    public function createGroups()
    {
        foreach ($this->groupData as $groupDatum) {
            $group = new Group();
            $group->setTitle($groupDatum['title']);
            $group->setType($groupDatum['type']);
            $group->setDescription($groupDatum['description']);

            $adminProfile = $this->profileRepository->findOneBy([
                'username' => $groupDatum['admin_username']
            ]);

            $group->setAdmin($adminProfile);
            $group->addProfile($adminProfile);
            $group->setCreatedAt(new DateTimeImmutable());

            $groupImage = $this->createImage($groupDatum['group_image']);

            $newGroupImage = $this->imageProcessor->saveImage(
                $groupImage,
                ImageProcessor::PROFILE_IMAGE_TYPE,
                '/public/images/group/'
            );
            $group->setGroupImageUrl('/images/group/' . $newGroupImage);

            foreach ($groupDatum['members_usernames'] as $memberUsername) {
                $memberProfile = $this->profileRepository->findOneBy([
                    'username' => $memberUsername
                ]);

                $group->addProfile($memberProfile);
            }

            $this->entityManager->persist($group);
        }
    }
}
