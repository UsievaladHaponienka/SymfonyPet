<?php

namespace App\Entity\Traits;

use App\Entity\Album;
use App\Entity\Group;
use App\Entity\Profile;

trait AlbumTrait
{
    abstract public function getType(): string;
    abstract public function getProfile(): ?Profile;
    abstract public function getRelatedGroup(): ?Group;

    public function isActionAllowed(Profile $profile): bool
    {
        if ($this->getType() == Album::USER_CUSTOM_TYPE) {
            /*
             * User custom albums can be deleted or edited by user
             */
            return $this->getProfile()->getId() == $profile->getId();
        } elseif ($this->getType() == Album::GROUP_CUSTOM_TYPE) {
            /*
             * Group custom albums can be deleted or edited by group admin
             */
            return $this->getRelatedGroup()->getAdmin()->getId() == $profile->getId();
        }

        return false;
    }

    public function isViewAllowed(Profile $profile): bool
    {
        //TODO: Profile privacy settings here
    }
}