<?php

namespace App\Entity\Traits;

use App\Entity\Like;
use App\Entity\Profile;
use Doctrine\Common\Collections\Collection;

/**
 * This trait contains custom methods for likeable entities - Comment and Post
 */
trait Likeable
{
    abstract public function getLikes(): Collection;

    /**
     * Returns Like created by $profile OR false if such like does not exist
     *
     * @param Profile $profile
     * @return Like|false
     */
    public function getLikeIfExists(Profile $profile): Like|false
    {
        return $this->getLikes()
            ->filter(function ($e) use ($profile) {
                /** @var Like $e */
                return $e->getProfile()->getId() == $profile->getId();
            })->first();
    }
}
