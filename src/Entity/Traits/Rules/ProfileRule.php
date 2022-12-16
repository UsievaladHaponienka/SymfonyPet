<?php

namespace App\Entity\Traits\Rules;

use App\Entity\Profile;

trait ProfileRule
{
    abstract public function getProfile(): ?Profile;

    /**
     * Check if entity belongs to $profile
     *
     * @param Profile $profile
     * @return bool
     */
    public function checkProfileRule(Profile $profile): bool
    {
        return $this->getProfile()->getId() == $profile->getId();
    }

}