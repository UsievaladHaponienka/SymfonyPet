<?php

namespace App\Entity\Traits;

use App\Entity\Album;
use App\Entity\FriendshipRequest;
use App\Entity\Group;
use App\Entity\Invite;
use Doctrine\Common\Collections\Collection;

/**
 * This trait contains custom methods for Profile entity.
 */
trait ProfileTrait
{
    abstract public function getFriendships(): Collection;

    abstract public function getRequestsMadeToProfile(): Collection;

    abstract public function getRequestsMadeByProfile(): Collection;

    abstract public function getGroups(): Collection;

    abstract public function getInvites(): Collection;

    abstract public function getAlbums(): Collection;

    /**
     * Check if profile with id = $friendId is a friend of current user
     *
     * @param int $friendId
     * @return bool
     */
    public function isFriend(int $friendId): bool
    {
        $friendships = $this->getFriendships()->filter(
            function ($friendship) use ($friendId) {
                return $friendship->getFriend()->getId() == $friendId;
            }
        );

        return (bool)$friendships->count();
    }

    /**
     * Check if current user has incoming friendship request from profile with id = $profileId
     *
     * @param int $profileId
     * @return bool
     */
    public function hasIncomingRequest(int $profileId): bool
    {
        return (bool)$this->getRequestsMadeToProfile()
            ->filter(
                function ($request) use ($profileId) {
                    /** @var FriendshipRequest $request */
                    return $request->getRequester()->getId() == $profileId;
                }
            )->count();
    }

    /**
     * Check if current user has outgoing friendship request to profile with id = $profileId
     *
     * @param int $profileId
     * @return bool
     */
    public function hasOutgoingRequest(int $profileId): bool
    {
        return (bool)$this->getRequestsMadeByProfile()
            ->filter(
                function ($request) use ($profileId) {
                    /** @var FriendshipRequest $request */
                    return $request->getRequestee()->getId() == $profileId;
                }
            )->count();
    }

    /**
     * Get collection of all groups administrated by current user
     *
     * @return Collection
     */
    public function getAdministratedGroups(): Collection
    {
        return $this
            ->getGroups()
            ->filter(function ($group) {
                /** @var Group $group */
                return $group->getAdmin()->getId() == $this->getId();
            });

    }

    /**
     * Check if current user has already been invited to group with id = $groupId
     *
     * @param int $groupId
     * @return bool
     */
    public function hasInvite(int $groupId): bool
    {
        return (bool)$this->getInviteByGroup($groupId);
    }

    /**
     * @param int $groupId
     * @return Invite|null
     */
    public function getInviteByGroup(int $groupId): ?Invite
    {
        return $this->getInvites()
            ->filter(
                function ($invite) use ($groupId) {
                    /** @var Invite $invite */
                    return $invite->getRelatedGroup()->getId() == $groupId;
                }
            )->first();
    }

    public function getDefaultAlbum(): Album
    {
        return $this->getAlbums()->filter(function ($album) {
            /** @var Album $album */
            return $album->getType() == Album::USER_DEFAULT_TYPE;
        })->first();
    }
}