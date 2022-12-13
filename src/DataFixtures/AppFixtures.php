<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Group;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\GroupRepository;
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

    private array $postData = [
        [
            'type' => 'group',
            'content' => 'First Metallica album',
            'group_title' => 'Metallica Fan Club',
            'photo' => 'kill_them_all'
        ],
        [
            'type' => 'group',
            'content' => 'Second Metallica album',
            'group_title' => 'Metallica Fan Club',
            'photo' => 'ride_the_lightning'
        ],
        [
            'type' => 'group',
            'content' => 'The best Slayer album',
            'group_title' => 'Slayer Fan Club',
            'photo' => 'reign_in_blood'
        ],
        [
            'type' => 'group',
            'content' => 'Helios Erebus by God Is An Astronaut, 2015 ',
            'group_title' => 'God Is An Astronaut Fan Club',
            'photo' => 'helios_erebus'
        ],
    ];


    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface      $entityManager,
        private readonly ImageProcessor              $imageProcessor,
        private readonly KernelInterface             $kernel,
        private readonly ProfileRepository           $profileRepository,
        private readonly GroupRepository             $groupRepository
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsers();
        $this->createGroups();
        $this->createPosts();

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

    public function createGroups(): void
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

            $defaultGroupAlbum = new Album();
            $defaultGroupAlbum->setType(Album::GROUP_DEFAULT_TYPE);
            $defaultGroupAlbum->setTitle(Album::DEFAULT_ALBUM_TITLE);

            $group->addAlbum($defaultGroupAlbum);

            foreach ($groupDatum['members_usernames'] as $memberUsername) {
                $memberProfile = $this->profileRepository->findOneBy([
                    'username' => $memberUsername
                ]);

                $group->addProfile($memberProfile);
            }

            $this->entityManager->persist($group);
        }

        $this->entityManager->flush();
    }

    protected function createPosts(): void
    {
        foreach ($this->postData as $postDatum) {
            $post = new Post();
            $post->setType($postDatum['type']);
            $post->setContent($postDatum['content']);
            $post->setCreatedAt(new DateTimeImmutable());

            if ($post->getType() == Post::GROUP_POST_TYPE) {
                $group = $this->groupRepository->findOneBy([
                    'title' => $postDatum['group_title']
                ]);
                $post->setRelatedGroup($group);
                $defaultAlbum = $group->getDefaultAlbum();
            } else {
                $profile = $this->profileRepository->findOneBy([
                    'username' => $postDatum['username']
                ]);
                $post->setProfile($profile);
                $defaultAlbum = $profile->getDefaultAlbum();
            }

            if($postDatum['photo']) {
                $image = $this->createImage($postDatum['photo']);

                $newImage = $this->imageProcessor->saveImage($image,
                ImageProcessor::POST_IMAGE_TYPE,
                    '/public/images/posts/');

                $photo = new Photo();
                $photo->setImageUrl('/images/posts/' . $newImage);
                $photo->setPost($post);
                $photo->setCreatedAt(new DateTimeImmutable());

                if ($post->getContent()) {
                    $photo->setDescription($post->getContent());
                }

                $defaultAlbum->addPhoto($photo);

                $this->entityManager->persist($post);
                $this->entityManager->persist($defaultAlbum);
            }
        }

        $this->entityManager->flush();
    }
}
