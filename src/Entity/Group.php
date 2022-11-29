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

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Album::class, cascade: ['remove'])]
    private Collection $albums;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Post::class, cascade: ['remove'])]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'groups')]
    private Collection $profile;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $admin = null;

    #[ORM\OneToMany(mappedBy: 'requestedGroup', targetEntity: GroupRequest::class)]
    private Collection $groupRequests;

    #[ORM\OneToMany(mappedBy: 'inviteGroup', targetEntity: GroupInvites::class)]
    private Collection $groupInvites;

    public function __construct()
    {
        $this->albums = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->profile = new ArrayCollection();
        $this->groupRequests = new ArrayCollection();
        $this->groupInvites = new ArrayCollection();
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

    public function getType(): ?string
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
            $album->setGroup($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getGroup() === $this) {
                $album->setGroup(null);
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
            $post->setGroup($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getGroup() === $this) {
                $post->setGroup(null);
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
            $groupRequest->setRequestedGroup($this);
        }

        return $this;
    }

    public function removeGroupRequest(GroupRequest $groupRequest): self
    {
        if ($this->groupRequests->removeElement($groupRequest)) {
            // set the owning side to null (unless already changed)
            if ($groupRequest->getRequestedGroup() === $this) {
                $groupRequest->setRequestedGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupInvites>
     */
    public function getGroupInvites(): Collection
    {
        return $this->groupInvites;
    }

    public function addGroupInvite(GroupInvites $groupInvite): self
    {
        if (!$this->groupInvites->contains($groupInvite)) {
            $this->groupInvites->add($groupInvite);
            $groupInvite->setInviteGroup($this);
        }

        return $this;
    }

    public function removeGroupInvite(GroupInvites $groupInvite): self
    {
        if ($this->groupInvites->removeElement($groupInvite)) {
            // set the owning side to null (unless already changed)
            if ($groupInvite->getInviteGroup() === $this) {
                $groupInvite->setInviteGroup(null);
            }
        }

        return $this;
    }


    public function getAdmin(): ?Profile
    {
        return $this->admin;
    }

    public function setAdmin(?Profile $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function isInGroup(Profile $profile): bool
    {
        $groupProfiles = $this
            ->getProfile()
            ->filter(function ($element) use ($profile){
                /** @var Profile $element */
                return $element->getId() == $profile->getId();
            });

        return (bool) $groupProfiles->count();
    }

    public function isRequested(User $user): bool
    {
        $groupRequests = $this
            ->getGroupRequests()
            ->filter(function ($element) use ($user){
                /** @var GroupRequest $element */
                return $element->getProfile()->getId() == $user->getProfile()->getId();
            });

        return (bool) $groupRequests->count();
    }
}
