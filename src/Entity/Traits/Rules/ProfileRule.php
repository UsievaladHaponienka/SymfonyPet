<?php

namespace App\Entity\Traits\Rules;

use App\Entity\Profile;

/**
 * This trait should be used for entities which belong or can belong to Profile with ManyToOne relation -
 * for example, Album, Post, Comment, etc.
 */
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
