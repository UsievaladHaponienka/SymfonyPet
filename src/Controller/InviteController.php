<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\InviteRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InviteController extends AbstractController
{
    public function __construct(
        private readonly ProfileRepository $profileRepository,
        private readonly GroupRepository   $groupRepository,
        private readonly InviteRepository  $inviteRepository
    )
    {
    }

    #[Route('invite/create/{profileId}/{groupId}', name: 'invite_create')]
    public function createInvite(int $profileId, int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->profileRepository->find($profileId);
        $group = $this->groupRepository->find($groupId);

        if ($profile && $group && $group->isAdmin($user->getProfile())) {
            $invite = new Invite();
            $invite->setProfile($profile);
            $invite->setRelatedGroup($group);

            $this->inviteRepository->save($invite, true);

            return new JsonResponse(['username' => $profile->getUsername()]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('invite/delete/{inviteId}', name: 'invite_delete')]
    public function deleteInvite(int $inviteId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $invite = $this->inviteRepository->find($inviteId);

        if ($invite && $invite->isActionAllowed($user->getProfile())){
            $this->inviteRepository->remove($invite, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('invite/accept/{inviteId}', name: 'invite_accept')]
    public function acceptInvite(int $inviteId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $invite = $this->inviteRepository->find($inviteId);

        if ($invite && $invite->getProfile()->getId() == $user->getProfile()->getId()) {
            $invite->getRelatedGroup()->addProfile($user->getProfile());
            $this->inviteRepository->remove($invite);
            $this->groupRepository->save($invite->getRelatedGroup(), true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }
}
