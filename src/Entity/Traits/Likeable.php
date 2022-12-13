<?php

namespace App\Entity\Traits;

use App\Entity\Like;
use App\Entity\Profile;
use Doctrine\Common\Collections\Collection;

trait Likeable
{
    abstract public function getLikes(): Collection;

    abstract public function addLike(Like $like): self;

    abstract public function removeLike(Like $like): self;

    public function getLikeIfExists(Profile $profile): Like|false
    {
        return $this->getLikes()
            ->filter(function ($e) use ($profile) {
                /** @var Like $e */
                return $e->getProfile()->getId() == $profile->getId();
            })->first();
    }
}
