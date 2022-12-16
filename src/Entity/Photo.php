<?php

namespace App\Entity;

use App\Entity\Interface\ViewableEntityInterface;
use App\Repository\PhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo implements ViewableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    #[ORM\Column(length: 255)]
    private ?string $image_url = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(inversedBy: 'photo', cascade: ['persist', 'remove'])]
    private ?Post $post = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(string $image_url): self
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function belongsToAlbumOnly(): bool
    {
        return $this->getPost() === null;
    }

    /**
     * Check if photo action is allowed for profile.
     * Action rules are the same as for album.
     * Current Photo actions: Delete Photo.
     *
     * @param Profile $profile
     * @return bool
     */
    public function isActionAllowed(Profile $profile): bool
    {
        if ($this->belongsToAlbumOnly()) {
            return $this->getAlbum()->isActionAllowed($profile);
        } else {
            return $this->getPost()->isActionAllowed($profile);
        }
    }

    /**
     * @inheritDoc
     * View riles are the same as for album
     */
    public function canBeViewed(Profile $profile): bool
    {
        return $this->getAlbum()->canBeViewed($profile);
    }
}
