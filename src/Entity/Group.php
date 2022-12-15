<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    public const PUBLIC_GROUP_TYPE = 'public';
    public const PRIVATE_GROUP_TYPE = 'private';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $group_image_url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'relatedGroup', targetEntity: Album::class, cascade: ['remove', 'persist'])]
    private Collection $albums;

    #[ORM\OneToMany(mappedBy: 'relatedGroup', targetEntity: Post::class, cascade: ['remove', 'persist'])]
    #[ORM\OrderBy(['created_at' => 'DESC'])]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'groups')]
    private Collection $profile;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $admin = null;

    #[ORM\OneToMany(mappedBy: 'relatedGroup', targetEntity: GroupRequest::class, cascade: ['remove', 'persist'])]
    private Collection $groupRequests;

    #[ORM\OneToMany(mappedBy: 'relatedGroup', targetEntity: Invite::class, cascade: ['remove'])]
    private Collection $invites;

    #[ORM\OneToMany(mappedBy: 'relatedGroup', targetEntity: Discussion::class, orphanRemoval: true)]
    private Collection $discussions;

    public function __construct()
    {
        $this->albums = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->profile = new ArrayCollection();
        $this->groupRequests = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->discussions = new ArrayCollection();
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

    public function getGroupImageUrl(): ?string
    {
        return $this->group_image_url;
    }

    public function setGroupImageUrl(?string $group_image_url): self
    {
        $this->group_image_url = $group_image_url;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

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
            $album->setRelatedGroup($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getRelatedGroup() === $this) {
                $album->setRelatedGroup(null);
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
            $post->setRelatedGroup($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getRelatedGroup() === $this) {
                $post->setRelatedGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getProfile(): Collection
    {
        return $this->profile;
    }

    public function addProfile(Profile $profile): self
    {
        if (!$this->profile->contains($profile)) {
            $this->profile->add($profile);
        }

        return $this;
    }

    public function removeProfile(Profile $profile): self
    {
        $this->profile->removeElement($profile);

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
            $groupRequest->setRelatedGroup($this);
        }

        return $this;
    }

    public function removeGroupRequest(GroupRequest $groupRequest): self
    {
        if ($this->groupRequests->removeElement($groupRequest)) {
            // set the owning side to null (unless already changed)
            if ($groupRequest->getRelatedGroup() === $this) {
                $groupRequest->setRelatedGroup(null);
            }
        }

        return $this;
    }

    public function getAdmin(): Profile
    {
        return $this->admin;
    }

    public function setAdmin(Profile $admin): self
    {
        $this->admin = $admin;

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
            $invite->setRelatedGroup($this);
        }

        return $this;
    }

    public function removeInvite(Invite $invite): self
    {
        if ($this->invites->removeElement($invite)) {
            // set the owning side to null (unless already changed)
            if ($invite->getRelatedGroup() === $this) {
                $invite->setRelatedGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setRelatedGroup($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getRelatedGroup() === $this) {
                $discussion->setRelatedGroup(null);
            }
        }

        return $this;
    }

    /**
     * Check if group type is public
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->getType() == self::PUBLIC_GROUP_TYPE;
    }

    /**
     * Check if $profile is admin of the group
     *
     * @param Profile $profile
     * @return bool
     */
    public function isAdmin(Profile $profile): bool
    {
        return $profile->getId() == $this->getAdmin()->getId();
    }

    /**
     * Check if $profile is a member of the group
     *
     * @param Profile $profile
     * @return bool
     */
    public function isInGroup(Profile $profile): bool
    {
        $groupProfiles = $this
            ->getProfile()
            ->filter(function ($element) use ($profile) {
                /** @var Profile $element */
                return $element->getId() == $profile->getId();
            });

        return (bool)$groupProfiles->count();
    }

    /**
     * Returns group request made by $profile OR false if such request does not exist
     *
     * @param Profile $profile
     * @return GroupRequest|false
     */
    public function getRequestByProfile(Profile $profile): GroupRequest|false
    {
        $requests = $this
            ->getGroupRequests()
            ->filter(function ($element) use ($profile) {
                /** @var GroupRequest $element */
                return $element->getProfile()->getId() == $profile->getId();
            });

        return $requests->first();
    }

    /**
     * Get default group album
     *
     * @return Album
     */
    public function getDefaultAlbum(): Album
    {
        return $this->getAlbums()->filter(function ($album) {
            /** @var Album $album */
            return $album->getType() == Album::GROUP_DEFAULT_TYPE;
        })->first();
    }


    /**
     * Check if group can be viewed by $profile.
     * Group can be viewed either if group is public or if user is member of the group.
     *
     * @param Profile $profile
     * @return bool
     */
    public function isViewAllowed(Profile $profile): bool
    {
        return $this->isPublic() || $this->isInGroup($profile);
    }
}
