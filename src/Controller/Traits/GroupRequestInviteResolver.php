<?php

namespace App\Controller\Traits;

use App\Entity\Group;
use App\Entity\GroupRequest;
use App\Entity\Invite;
use App\Entity\Profile;

trait GroupRequestInviteResolver
{
    /**
     * Check if Group Request from $profile to $group already exists.
     *
     * @param Group $group
     * @param Profile $profile
     * @return GroupRequest|false
     */
    public function getRequestIfExists(Group $group, Profile $profile): GroupRequest|false
    {
        return $group->getGroupRequests()->filter(
            function ($request) use ($profile) {
                /** @var GroupRequest $request */
                return $request->getProfile()->getId() == $profile->getId();
            })->first();
    }

    /**
     * Check if Invite from $profile to $group already exists.
     *
     * @param Group $group
     * @param Profile $profile
     * @return Invite|false
     */
    public function getInviteIfExists(Group $group, Profile $profile): Invite|false
    {
        return $group->getInvites()->filter(
            function ($invite) use ($profile) {
                /** @var Invite $invite */
                return $invite->getProfile()->getId() == $profile->getId();
            })->first();
    }

    /**
     * Check if new Group Request or Invite can be created. The following conditions must be fulfilled:
     * - Group type is private.
     * - Profile is NOT already in Group.
     * - Same Request/Invite does not exist.
     * - Similar Invite/Request does not exist.
     *
     * @param Group $group
     * @param Profile $profile
     * @return bool
     */
    protected function canCreateRequestOrInvite(Group $group, Profile $profile): bool
    {
        return !$group->isPublic() &&
            !$group->isInGroup($profile) &&
            !$this->getRequestIfExists($group, $profile) &&
            !$this->getInviteIfExists($group, $profile);
    }
}