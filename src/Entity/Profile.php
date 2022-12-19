<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profile_image_url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Album::class, cascade: ['persist', 'remove'])]
    private Collection $albums;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Post::class)]
    #[ORM\OrderBy(['created_at' => 'DESC'])]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'profile')]
    private Collection $groups;

    #[ORM\OneToMany(mappedBy: 'requester', targetEntity: FriendshipRequest::class)]
    private Collection $requester;

    #[ORM\OneToMany(mappedBy: 'requestee', targetEntity: FriendshipRequest::class)]
    private Collection $requestee;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Friendship::class)]
    private Collection $friendships;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: GroupRequest::class)]
    private Collection $groupRequests;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Invite::class)]
    private Collection $invites;

    #[ORM\OneToOne(mappedBy: 'profile', cascade: ['persist', 'remove'])]
    private ?PrivacySettings $privacySettings = null;

    public function __construct()
    {
        $this->albums = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->requester = new ArrayCollection();
        $this->requestee = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->groupRequests = new ArrayCollection();
        $this->invites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getProfileImageUrl(): ?string
    {
        return $this->profile_image_url;
    }

    public function setProfileImageUrl(?string $profile_image_url): self
    {
        $this->profile_image_url = $profile_image_url;

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
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->setProfile($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            if ($album->getProfile() === $this) {
                $album->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setProfile($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getProfile() === $this) {
                $post->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProfile($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getProfile() === $this) {
                $comment->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addProfile($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            $group->removeProfile($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendshipRequest>
     */
    public function getRequestsMadeByProfile(): Collection
    {
        return $this->requester;
    }

    public function addRequester(FriendshipRequest $requester): self
    {
        if (!$this->requester->contains($requester)) {
            $this->requester->add($requester);
            $requester->setRequester($this);
        }

        return $this;
    }

    public function removeRequester(FriendshipRequest $requester): self
    {
        if ($this->requester->removeElement($requester)) {
            if ($requester->getRequester() === $this) {
                $requester->setRequester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendshipRequest>
     */
    public function getRequestsMadeToProfile(): Collection
    {
        return $this->requestee;
    }

    public function addRequestee(FriendshipRequest $requestee): self
    {
        if (!$this->requestee->contains($requestee)) {
            $this->requestee->add($requestee);
            $requestee->setRequestee($this);
        }

        return $this;
    }

    public function removeRequestee(FriendshipRequest $requestee): self
    {
        if ($this->requestee->removeElement($requestee)) {
            // set the owning side to null (unless already changed)
            if ($requestee->getRequestee() === $this) {
                $requestee->setRequestee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    public function addFriendship(Friendship $friendship): self
    {
        if (!$this->friendships->contains($friendship)) {
            $this->friendships->add($friendship);
            $friendship->setProfile($this);
        }

        return $this;
    }

    public function removeFriendship(Friendship $friendship): self
    {
        if ($this->friendships->removeElement($friendship)) {
            if ($friendship->getProfile() === $this) {
                $friendship->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupRequest>
     */
    public function getGroupRequests(): Collection
    {
        return $this->groupRequests;
    }

    public function addGroupRequest(GroupRequest $groupRequest): self
    {
        if (!$this->groupRequests->contains($groupRequest)) {
            $this->groupRequests->add($groupRequest);
            $groupRequest->setProfile($this);
        }

        return $this;
    }

    public function removeGroupRequest(GroupRequest $groupRequest): self
    {
        if ($this->groupRequests->removeElement($groupRequest)) {
            if ($groupRequest->getProfile() === $this) {
                $groupRequest->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invite>
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    public function addInvite(Invite $invite): self
    {
        if (!$this->invites->contains($invite)) {
            $this->invites->add($invite);
            $invite->setProfile($this);
        }

        return $this;
    }

    public function removeInvite(Invite $invite): self
    {
        if ($this->invites->removeElement($invite)) {
            if ($invite->getProfile() === $this) {
                $invite->setProfile(null);
            }
        }

        return $this;
    }

    public function getPrivacySettings(): ?PrivacySettings
    {
        return $this->privacySettings;
    }

    public function setPrivacySettings(PrivacySettings $privacySettings): self
    {
        if ($privacySettings->getProfile() !== $this) {
            $privacySettings->setProfile($this);
        }

        $this->privacySettings = $privacySettings;

        return $this;
    }

    /**
     * Check if profile with id = $friendId is a friend of current user
     *
     * @param int $friendId
     * @return bool
     */
    public function isFriend(int $friendId): bool
    {
        $friendships = $this->getFriendships()->filter(
            function ($friendship) use ($friendId) {
                return $friendship->getFriend()->getId() == $friendId;
            }
        );

        return (bool)$friendships->count();
    }

    /**
     * Check if current user has incoming friendship request from profile with id = $profileId
     *
     * @param int $profileId
     * @return bool
     */
    public function hasIncomingRequest(int $profileId): bool
    {
        return (bool)$this->getRequestsMadeToProfile()
            ->filter(
                function ($request) use ($profileId) {
                    /** @var FriendshipRequest $request */
                    return $request->getRequester()->getId() == $profileId;
                }
            )->count();
    }

    /**
     * Check if current user has outgoing friendship request to profile with id = $profileId
     *
     * @param int $profileId
     * @return bool
     */
    public function hasOutgoingRequest(int $profileId): bool
    {
        return (bool)$this->getRequestsMadeByProfile()
            ->filter(
                function ($request) use ($profileId) {
                    /** @var FriendshipRequest $request */
                    return $request->getRequestee()->getId() == $profileId;
                }
            )->count();
    }

    /**
     * Get collection of all groups administrated by current user
     *
     * @return Collection
     */
    public function getAdministratedGroups(): Collection
    {
        return $this
            ->getGroups()
            ->filter(function ($group) {
                /** @var Group $group */
                return $group->getAdmin()->getId() == $this->getId();
            });
    }

    /**
     * Get invite for current user from group with id = $groupId
     *
     * @param int $groupId
     * @return Invite|false
     */
    public function getInviteByGroup(int $groupId): Invite|false
    {
        return $this->getInvites()
            ->filter(
                function ($invite) use ($groupId) {
                    /** @var Invite $invite */
                    return $invite->getRelatedGroup()->getId() == $groupId;
                }
            )->first();
    }

    /**
     * Check if current user has already been invited to group with id = $groupId
     *
     * @param int $groupId
     * @return bool
     */
    public function hasInvite(int $groupId): bool
    {
        return (bool)$this->getInviteByGroup($groupId);
    }

    /**
     * Get default profile album
     *
     * @return Album
     */
    public function getDefaultAlbum(): Album
    {
        return $this->getAlbums()->filter(function ($album) {
            /** @var Album $album */
            return $album->getType() == Album::USER_DEFAULT_TYPE;
        })->first();
    }
}
