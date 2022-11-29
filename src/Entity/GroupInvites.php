<?php

namespace App\Entity;

use App\Repository\GroupInvitesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupInvitesRepository::class)]
class GroupInvites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupInvites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $inviteGroup = null;

    #[ORM\ManyToOne(inversedBy: 'groupInvites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInviteGroup(): ?Group
    {
        return $this->inviteGroup;
    }

    public function setInviteGroup(?Group $inviteGroup): self
    {
        $this->inviteGroup = $inviteGroup;

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
}
