<?php

namespace App\Controller;

use App\Controller\Traits\GroupRequestInviteResolver;
use App\Entity\GroupRequest;
use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MembershipController extends AbstractController
{
    use GroupRequestInviteResolver;

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

        if ($group && $this->canCreateRequestOrInvite($group, $user->getProfile())) {
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

        if ($joinRequest &&
            $joinRequest->isActionAllowed($user->getProfile(), IEInterface::DELETE_ACTION_CODE)) {
            $this->groupRequestRepository->remove($joinRequest, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/accept/{requestId}', name: 'group_request_accept')]
    public function acceptJoinRequest(int $requestId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $joinRequest = $this->groupRequestRepository->find($requestId);

        if ($joinRequest && $joinRequest->getRelatedGroup() &&
            $joinRequest->isActionAllowed($user->getProfile(), IEInterface::ACCEPT_ACTION_CODE)) {

            $joinRequest->getRelatedGroup()->addProfile($joinRequest->getProfile());
            $this->groupRequestRepository->remove($joinRequest);

            $this->groupRepository->save($joinRequest->getRelatedGroup(), true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/remove/{groupId}/{profileId}', name: 'group_remove')]
    public function removeFromGroup(int $groupId, int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);
        $profile = $this->profileRepository->find($profileId);

        if ($group && $profile && $group->isInGroup($profile) && $group->isAdmin($user->getProfile())) {
            $group->removeProfile($profile);
            $this->groupRepository->save($group, true);

            return new JsonResponse(['username' => $profile->getUsername()]);
        }

        throw $this->createNotFoundException();
    }
}
