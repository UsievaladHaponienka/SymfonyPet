<?php

namespace App\Controller\Group;

use App\Controller\BaseController;
use App\Entity\Group;
use App\Entity\GroupRequest;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\GroupRequestRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupInteractionController extends BaseController
{
    private GroupRepository $groupRepository;

    private GroupRequestRepository $groupRequestRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupRequestRepository $groupRequestRepository,
    )
    {
        $this->groupRepository = $groupRepository;
        $this->groupRequestRepository = $groupRequestRepository;
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
}
