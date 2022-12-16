<?php

namespace App\Entity\Traits\Rules;

use App\Entity\Group;
use App\Entity\Profile;

/**
 * This trait should be used for entities which belong or can belong to Group with ManyToOne relation -
 * for example, Album, Discussion, GroupRequest, etc.
 */
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