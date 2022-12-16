<?php

namespace App\Entity;

use App\Entity\Traits\Rules\GroupAdminRule;
use App\Entity\Traits\Rules\ProfileRule;
use App\Repository\InviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InviteRepository::class)]
class Invite
{
    public const DELETE_ACTION = 'delete';
    public const ACCEPT_ACTION = 'accept';

    use ProfileRule;
    use GroupAdminRule;

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
     * @param string|null $actionCode
     * @return bool
     */
    public function isActionAllowed(Profile $profile, string $actionCode = null): bool
    {
        return match ($actionCode) {
            self::ACCEPT_ACTION => $this->checkProfileRule($profile),
            default => $this->checkProfileRule($profile) || $this->checkGroupAdminRule($profile)
        };
    }


}
