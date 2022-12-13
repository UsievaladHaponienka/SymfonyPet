<?php

namespace App\Entity\Traits;

use App\Entity\Group;
use App\Entity\Profile;

trait GroupOwned
{
    abstract public function getRelatedGroup(): ?Group;

    public function isGroupActionAllowed(Profile $profile): bool
    {
        return $this->getRelatedGroup()->getAdmin()->getId() == $profile->getId();
    }
}