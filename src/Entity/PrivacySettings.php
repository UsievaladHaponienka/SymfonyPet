<?php

namespace App\Entity;

use App\Repository\PrivacySettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrivacySettingsRepository::class)]
class PrivacySettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'privacySettings', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: false)]
    private ?int $friendList = 2;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: false)]
    private ?int $groupList = 2;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: false)]
    private ?int $albums = 2;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: false)]
    private ?int $posts = 2;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getFriendList(): ?int
    {
        return $this->friendList;
    }

    public function setFriendList(int $friendList): self
    {
        $this->friendList = $friendList;

        return $this;
    }

    public function getGroupList(): ?int
    {
        return $this->groupList;
    }

    public function setGroupList(int $groupList): self
    {
        $this->groupList = $groupList;

        return $this;
    }

    public function getAlbums(): ?int
    {
        return $this->albums;
    }

    public function setAlbums(int $albums): self
    {
        $this->albums = $albums;

        return $this;
    }

    public function getPosts(): ?int
    {
        return $this->posts;
    }

    public function setPosts(int $posts): self
    {
        $this->posts = $posts;

        return $this;
    }
}