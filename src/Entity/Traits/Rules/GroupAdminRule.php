<?php

namespace App\Entity\Traits\Rules;

use App\Entity\Group;
use App\Entity\Profile;

trait GroupAdminRule
{
    abstract public function getRelatedGroup(): ?Group;

    /**
     * Check if entity belongs to group which s administrated by $profile
     *
     * @param Profile $profile
     * @return bool
     */
    public function checkGroupAdminRule(Profile $profile): bool
    {
        return $this->getRelatedGroup()->getAdmin()->getId() == $profile->getId();
    }
}