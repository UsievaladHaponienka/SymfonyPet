<?php

namespace App\EntityInterface;

use App\Entity\Like;
use App\Entity\Profile;
use Doctrine\Common\Collections\Collection;

interface LikeableInterface
{
    /**
     * Check if current entity is liked by $profile
     *
     * @param Profile $profile
     * @return bool
     */
    public function isLikedBy(Profile $profile): bool;

    /**
     * @return Collection
     */
    public function getLikes(): Collection;

    /**
     * @param Like $like
     * @return $this
     */
    public function addLike(Like $like): self;

    /**
     * @param Like $like
     * @return $this
     */
    public function removeLike(Like $like): self;
}
