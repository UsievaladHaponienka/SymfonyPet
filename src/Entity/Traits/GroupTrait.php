<?php

namespace App\Entity\Traits;

use App\Entity\Album;
use App\Entity\GroupRequest;
use App\Entity\Profile;
use Doctrine\Common\Collections\Collection;

/**
 * This trait contains custom methods for Group entity
 */
trait GroupTrait
{
    abstract public function getAdmin(): Profile;

    abstract public function getProfile(): Collection;

    abstract public function getType(): string;

    abstract public function getGroupRequests(): Collection;

    abstract public function getAlbums(): Collection;

    //TODO:: This method should be implemented inside the trait using group type constants
    //PHP should be updated to 8.2, which allows to use constants in traits
    abstract public function isPublic(): bool;

    public function isAdmin(Profile $profile): bool
    {
        return $profile->getId() == $this->getAdmin()->getId();
    }

    /**
     * Check if profile with id = $profileId is already in group
     *
     * @param Profile $profile
     * @return bool
     */
    public function isInGroup(Profile $profile): bool
    {
        $groupProfiles = $this
            ->getProfile()
            ->filter(function ($element) use ($profile){
                /** @var Profile $element */
                return $element->getId() == $profile->getId();
            });

        return (bool) $groupProfiles->count();
    }

    /**
     * Returns group request made by $profile OR false if such request does not exist
     *
     * @param Profile $profile
     * @return GroupRequest|false
     */
    public function getRequestByProfile(Profile $profile): GroupRequest|false
    {
        $requests = $this
            ->getGroupRequests()
            ->filter(function ($element) use ($profile){
                /** @var GroupRequest $element */
                return $element->getProfile()->getId() == $profile->getId();
            });

        return $requests->first();
    }

    public function getDefaultAlbum(): Album
    {
        return $this->getAlbums()->filter(function ($album) {
            /** @var Album $album */
            return $album->getType() == Album::GROUP_DEFAULT_TYPE;
        })->first();
    }

    /**
     * View is allowed if:
     * 1. Group is public
     * 2. User is in group
     *
     * @param Profile $profile
     * @return bool
     */
    public function isViewAllowed(Profile $profile): bool
    {
        return $this->isPublic() || $this->isInGroup($profile);
    }
}
