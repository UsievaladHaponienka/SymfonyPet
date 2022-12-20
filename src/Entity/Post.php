<?php

namespace App\Entity;

use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\Traits\Likeable;
use App\Entity\Traits\Rules\ProfileRule;
use App\Entity\Traits\Rules\GroupAdminRule;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post implements IEInterface
{
    use ProfileRule;
    use GroupAdminRule;
    use Likeable;

    public const USER_POST_TYPE = 'user';
    public const GROUP_POST_TYPE = 'group';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?Profile $profile = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?Group $relatedGroup = null;

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

    public function getRelatedGroup(): ?Group
    {
        return $this->relatedGroup;
    }

    public function setRelatedGroup(?Group $relatedGroup): self
    {
        $this->relatedGroup = $relatedGroup;

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
        if ($photo === null && $this->photo !== null) {
            $this->photo->setPost(null);
        }

        if ($photo !== null && $photo->getPost() !== $this) {
            $photo->setPost($this);
        }

        $this->photo = $photo;

        return $this;
    }

    public function belongsToUser(): bool
    {
        return $this->getType() == self::USER_POST_TYPE;
    }

    /**
     * @inheritDoc
     *
     * ACTIONS:
     *
     * VIEW ACTION:
     * - Posts with type = profile can be viewed if corresponding profile privacy settings requirement is fulfilled.
     * - Posts with type = group can be viewed either if group is public OR if user is member of the group.
     *
     * OTHER ACTIONS:
     * - For Posts with type = profile actions are allowed to post owner's profile.
     * - For Posts with type = group actions are allowed to group admin.
     */
    public function isActionAllowed(Profile $profile, string $actionCode = null): bool
    {
        if ($actionCode == IEInterface::VIEW_ACTION_CODE) {
            if($this->belongsToUser()) {
                return $this->getProfile()->getPrivacySettings()->isPostViewAllowed($profile);
            } else {
                return $this->getRelatedGroup()->isActionAllowed($profile, $actionCode);
            }
        }

        if($this->belongsToUser()) {
            return $this->checkProfileRule($profile);
        } else {
            return $this->checkGroupAdminRule($profile);
        }
    }
}
