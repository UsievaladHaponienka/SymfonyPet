<?php

namespace App\Entity\Interface;

use App\Entity\Profile;

/**
 * Each entity, which can viewed separately only under certain condition (for example, Album, Photo, Group),
 * should implement this interface
 */
interface ViewableEntityInterface
{
    /**
     * Check if entity can be viewed by $profile
     *
     * @param Profile $profile
     * @return bool
     */
    public function canBeViewed(Profile $profile): bool;
}