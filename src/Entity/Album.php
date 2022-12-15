<?php

namespace App\Entity;

use App\Entity\Interface\ViewableEntityInterface;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album implements ViewableEntityInterface
{
    public const USER_DEFAULT_TYPE = 'user_posts';
    public const USER_CUSTOM_TYPE = 'user_custom';

    public const GROUP_DEFAULT_TYPE = 'group_posts';
    public const GROUP_CUSTOM_TYPE = 'group_custom';

    public const DEFAULT_ALBUM_TITLE = 'Posts photo';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'albums')]
    private ?Profile $profile = null;

    #[ORM\ManyToOne(inversedBy: 'albums')]
    private ?Group $relatedGroup = null;

    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Photo::class, cascade: ['remove', 'persist'])]
    private Collection $photos;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getRelatedGroup(): ?Group
    {
        return $this->relatedGroup;
    }

    public function setRelatedGroup(?Group $relatedGroup): self
    {
        $this->relatedGroup = $relatedGroup;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setAlbum($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getAlbum() === $this) {
                $photo->setAlbum(null);
            }
        }

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

    /**
     * Check if album action - editing, deleting, adding new photos - is allowed for $profile.
     * User custom albums actions are allowed for albums owner's profile.
     * Group custom albums actions are allowed for group admin.
     *
     * @param Profile $profile
     * @return bool
     */
    public function isActionAllowed(Profile $profile): bool
    {
        if ($this->getType() == Album::USER_CUSTOM_TYPE) {
            return $this->getProfile()->getId() == $profile->getId();
        } elseif ($this->getType() == Album::GROUP_CUSTOM_TYPE) {
            return $this->getRelatedGroup()->getAdmin()->getId() == $profile->getId();
        }

        return false;
    }

    /**
     * @inheritDoc
     * Profile albums can be viewed if corresponding profile privacy settings requirement is fulfilled.
     * Group albums can be viewed either if group is public or if user is member of the group.
     *
     * @param Profile $profile
     * @return bool
     */
    public function canBeViewed(Profile $profile): bool
    {
        if ($this->getType() == Album::GROUP_CUSTOM_TYPE || $this->getType() == Album::GROUP_DEFAULT_TYPE) {
            return $this->getRelatedGroup()->isPublic() || $this->getRelatedGroup()->isInGroup($profile);
        } else {
            return $this->getProfile()->getPrivacySettings()->isViewAllowed(
                PrivacySettings::ALBUMS_CODE, $profile
            );
        }
    }
}
