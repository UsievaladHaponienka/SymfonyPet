<?php

namespace App\Entity\Traits;

use App\Entity\Profile;
use App\Entity\User;

trait ProfileOwned
{
    abstract public function getProfile(): ?Profile;

    public function isProfileActionAllowed(Profile $profile): bool
    {
        return $this->getProfile()->getId() == $profile->getId();
    }
}