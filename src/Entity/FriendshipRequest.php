<?php

namespace App\Entity;

use App\Repository\FriendshipRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendshipRequestRepository::class)]
class FriendshipRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'requester')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $requester = null;

    #[ORM\ManyToOne(inversedBy: 'requestee')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $requestee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequester(): ?Profile
    {
        return $this->requester;
    }

    public function setRequester(?Profile $requester): self
    {
        $this->requester = $requester;

        return $this;
    }

    public function getRequestee(): ?Profile
    {
        return $this->requestee;
    }

    public function setRequestee(?Profile $requestee): self
    {
        $this->requestee = $requestee;

        return $this;
    }
}
