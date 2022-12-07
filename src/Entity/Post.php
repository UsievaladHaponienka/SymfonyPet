<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    public const USER_POST_TYPE = 'user';
    public const GROUP_POST_TYPE = 'group';

    public const LIKED_BUTTON_STYLE = 'bg-sky-500 hover:bg-sky-400';
    public const NOT_LIKED_BUTTON_STYLE = 'bg-sky-900 hover:bg-sky-800';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?Profile $profile = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?Group $group = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_edited = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Like::class, cascade: ['remove', 'persist'], orphanRemoval: true)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, cascade: ['remove', 'persist'], orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToOne(mappedBy: 'post', cascade: ['persist', 'remove'])]
    private ?Photo $photo = null;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isIsEdited(): ?bool
    {
        return $this->is_edited;
    }

    public function setIsEdited(?bool $is_edited): self
    {
        $this->is_edited = $is_edited;

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
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        // unset the owning side of the relation if necessary
        if ($photo === null && $this->photo !== null) {
            $this->photo->setPost(null);
        }

        // set the owning side of the relation if necessary
        if ($photo !== null && $photo->getPost() !== $this) {
            $photo->setPost($this);
        }

        $this->photo = $photo;

        return $this;
    }

    /**
     * Check if this post can be deleted by current user.
     *
     * @param User $user
     * @return bool
     */
    public function canBeDeleted(User $user): bool
    {
        if($this->getGroup()) {
            return $this->getGroup()->getAdmin()->getId() == $user->getProfile()->getId();
        } else {
            return $this->getProfile()->getId() == $user->getProfile()->getId();
        }
    }

    public function isLikedBy(Profile $profile): bool
    {
        $likes = $this->getLikes()->filter(function ($element) use ($profile) {
            /** @var Like $element */
            return $element->getProfile()->getId() == $profile->getId();
        });

        return (bool) $likes->count();
    }

    public function getLikeButtonStyle(Profile $profile): string
    {
        return $this->isLikedBy($profile) ? self::LIKED_BUTTON_STYLE : self::NOT_LIKED_BUTTON_STYLE;
    }

    public function getLikeButtonText(Profile $profile): string
    {
        if ($this->isLikedBy($profile)) {
            return 'Liked (' .  $this->getLikes()->count() . ')';
        }
        return 'Like (' .  $this->getLikes()->count() . ')';
    }
}
