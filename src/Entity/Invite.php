<?php

namespace App\Entity;

use App\Repository\InviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InviteRepository::class)]
class Invite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    #[ORM\ManyToOne(inversedBy: 'invites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $relatedGroup = null;

    public function getId(): ?int
    {
        return $this->id;
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
     * Check if invite action is allowed for profile.
     * Action is allowed either for invited profile of for invite group admin
     *
     * @param Profile $profile
     * @return bool
     */
    public function isActionAllowed(Profile $profile): bool
    {
        return $this->getProfile()->getId() == $profile->getId() || $this->getRelatedGroup()->isAdmin($profile);
    }
}
