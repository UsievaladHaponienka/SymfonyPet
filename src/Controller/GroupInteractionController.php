<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\GroupRequest;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupInteractionController extends BaseController
{
    private GroupRepository $groupRepository;

    private GroupRequestRepository $groupRequestRepository;

    private ProfileRepository $profileRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupRequestRepository $groupRequestRepository,
        ProfileRepository $profileRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->groupRequestRepository = $groupRequestRepository;
        $this->profileRepository = $profileRepository;
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
            $joinRequest->setRequestedGroup($group);

            $this->groupRequestRepository->save($joinRequest, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/cancel/{groupId}', name: 'group_request_cancel')]
    public function cancelJoinRequest(int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group) {
            $joinRequest = $this->groupRequestRepository->findOneBy([
                'profile' => $user->getProfile(),
                'requestedGroup' => $group->getId()
            ]);

            if ($joinRequest && $joinRequest->getProfile()->getId() == $user->getProfile()->getId()) {
                $this->groupRequestRepository->remove($joinRequest, true);

                return new JsonResponse();
            }

            throw $this->createNotFoundException();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/decline/{requestId}', name: 'group_request_decline')]
    public function declineJoinRequest(int $requestId): Response
    {
        $joinRequest = $this->groupRequestRepository->find($requestId);

        if ($joinRequest && $this->isAdmin($joinRequest->getRequestedGroup())) {
            $groupId = $joinRequest->getRequestedGroup()->getId();
            $this->groupRequestRepository->remove($joinRequest, true);

            return $this->redirectToRoute('group_edit', [
                'groupId' => $groupId
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/accept/{requestId}', name: 'group_request_accept')]
    public function acceptJoinRequest(int $requestId): Response
    {
        $joinRequest = $this->groupRequestRepository->find($requestId);

        if ($joinRequest) {
            $group = $this->groupRepository->find($joinRequest->getRequestedGroup());

            if ($group && $this->isAdmin($group)) {
                $group->addProfile($joinRequest->getProfile());
                $this->groupRequestRepository->remove($joinRequest);

                $this->groupRepository->save($group, true);

                return $this->redirectToRoute('group_edit', ['groupId' => $group->getId()]);
            }

            //TODO: Maybe refactor, I don't like 2 throws in method. Same for method below
            throw $this->createNotFoundException();
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/remove/{groupId}/{profileId}', name: 'group_remove')]
    public function removeFromGroup(int $groupId, int $profileId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group && $this->isAdmin($group)) {
            $profile = $this->profileRepository->find($profileId);

            if ($profile) {
                $group->removeProfile($profile);
                $this->groupRepository->save($group, true);

                return $this->redirectToRoute('group_edit', ['groupId' => $group->getId()]);
            }

            throw $this->createNotFoundException();
        }

        throw $this->createNotFoundException();
    }

    protected function isAdmin(Group $group): bool
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user->getProfile()->getId() == $group->getAdmin()->getId();
    }
}
