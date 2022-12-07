<?php

namespace App\Controller\Group;

use App\Entity\Group;
use App\Entity\GroupRequest;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembershipController extends BaseGroupController
{
    public function __construct(
        private readonly GroupRepository        $groupRepository,
        private readonly GroupRequestRepository $groupRequestRepository,
        private readonly ProfileRepository      $profileRepository,
    )
    {
    }

    #[Route('group/join/{groupId}', name: 'group_join')]
    public function joinGroup(int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group) {
            /** @var User $user */
            $user = $this->getUser();
            $group->addProfile($user->getProfile());

            $this->groupRepository->save($group, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/leave/{groupId}', name: 'group_leave')]
    public function leaveGroup(int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);
        /** @var User $user */
        $user = $this->getUser();

        if ($group && $group->isInGroup($user->getProfile())) {
            $group->removeProfile($user->getProfile());
            $this->groupRepository->save($group, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/create/{groupId}', name: 'group_request_create')]
    public function createJoinRequest(int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $group->getType() == Group::PRIVATE_GROUP_TYPE && !$group->isInGroup($user->getProfile())) {
            $joinRequest = new GroupRequest();
            $joinRequest->setProfile($user->getProfile());
            $joinRequest->setRelatedGroup($group);

            $this->groupRequestRepository->save($joinRequest, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/delete/{requestId}', name: 'group_request_delete')]
    public function deleteJoinRequest(int $requestId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $joinRequest = $this->groupRequestRepository->find(($requestId));

        if ($joinRequest && (
                $joinRequest->getProfile()->getId() == $user->getProfile()->getId() ||
                $this->isAdmin($joinRequest->getRelatedGroup())
            )) {
            $this->groupRequestRepository->remove($joinRequest, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/accept/{requestId}', name: 'group_request_accept')]
    public function acceptJoinRequest(int $requestId): Response
    {
        $joinRequest = $this->groupRequestRepository->find($requestId);

        if ($joinRequest) {
            $group = $this->groupRepository->find($joinRequest->getRelatedGroup());

            if ($group && $this->isAdmin($group)) {
                $group->addProfile($joinRequest->getProfile());
                $this->groupRequestRepository->remove($joinRequest);

                $this->groupRepository->save($group, true);

                return new JsonResponse();
            }

            //TODO: Maybe refactor, I don't like 2 throws in method. Same for method above
            throw $this->createNotFoundException();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/remove/{groupId}/{profileId}', name: 'group_remove')]
    public function removeFromGroup(int $groupId, int $profileId): Response
    {
        $group = $this->groupRepository->find($groupId);
        $profile = $this->profileRepository->find($profileId);

        if ($group && $profile && $group->isInGroup($profile) && $this->isAdmin($group)) {
            $group->removeProfile($profile);
            $this->groupRepository->save($group, true);

            return new JsonResponse(['username' => $profile->getUsername()]);
        }

        throw $this->createNotFoundException();
    }
}