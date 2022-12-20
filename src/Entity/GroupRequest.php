<?php

namespace App\Entity;

use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\Traits\Rules\ProfileRule;
use App\Entity\Traits\Rules\GroupAdminRule;
use App\Repository\GroupRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRequestRepository::class)]
class GroupRequest implements IEInterface
{
    use ProfileRule;
    use GroupAdminRule;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $relatedGroup = null;

    #[ORM\ManyToOne(inversedBy: 'groupRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @inheritDoc
     *
     * ACTIONS:
     * - Request can be created if group is private AND user is not in group.
     * - Request can be accepted by group admin.
     * - Request can be deleted either by request group admin or by request creator
     */
    public function isActionAllowed(Profile $profile, string $actionCode = null): bool
    {
        return match ($actionCode) {
            self::ACCEPT_ACTION_CODE => $this->checkGroupAdminRule($profile),
            default => $this->checkProfileRule($profile) || $this->checkGroupAdminRule($profile),
        };
    }
}
