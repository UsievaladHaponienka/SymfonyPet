<?php

namespace App\Entity;

use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\Traits\Likeable;
use App\Entity\Traits\Rules\ProfileRule;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment implements IEInterface
{
    use Likeable;
    use ProfileRule;

    public const POST_TYPE = 'post';
    public const DISCUSSION_TYPE = 'discussion';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Discussion $discussion = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: Like::class, cascade: ['remove', 'persist'])]
    private Collection $likes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): self
    {
        $this->discussion = $discussion;

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
            $like->setComment($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getComment() === $this) {
                $like->setComment(null);
            }
        }

        return $this;
    }

    public function belongsToDiscussion(): bool
    {
        return $this->getType() == Comment::DISCUSSION_TYPE;
    }

    /**
     * @inheritDoc
     *
     * ACTIONS:
     * - Discussion comment actions are allowed for comment author OR discussion group admin.
     * - Group post comment actions are allowed for comment author and group admin.
     * - Profile post comment actions are allowed for comment author and post profile.
     */
    public function isActionAllowed(Profile $profile, $actionCode = null): bool
    {
        if ($this->belongsToDiscussion()) {
            return $this->checkProfileRule($profile) || $this->getDiscussion()->isActionAllowed($profile);
        } else {
            return $this->checkProfileRule($profile) || $this->getPost()->isActionAllowed($profile);
        }
    }
}
