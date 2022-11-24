<?php

namespace App\Controller;

use App\Entity\FriendshipRequest;
use App\Entity\User;
use App\Repository\FriendshipRequestRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    private ProfileRepository $profileRepository;
    private FriendshipRequestRepository $friendshipRequestRepository;

    public function __construct(
        ProfileRepository $profileRepository,
        FriendshipRequestRepository $friendshipRequestRepository
    ){
        $this->profileRepository = $profileRepository;
        $this->friendshipRequestRepository = $friendshipRequestRepository;
    }

    #[Route('friendship-request/create/{profileId}', name: 'friendship_request_create')]
    public function createRequest(int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $requesterProfile = $this->profileRepository->find($user->getProfile()->getId());
        $requesteeProfile = $this->profileRepository->find($profileId);

        if ($requesterProfile && $requesteeProfile) {
            $friendshipRequest = new FriendshipRequest();
            $friendshipRequest->setRequester($requesterProfile);
            $friendshipRequest->setRequestee($requesteeProfile);

            $this->friendshipRequestRepository->save($friendshipRequest, true);

            return $this->redirectToRoute('profile_index', ['profileId' => $profileId]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship-request/delete/{requestId}', name: 'friendship_request_delete')]
    public function deleteRequest(int $requestId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $request = $this->friendshipRequestRepository->find($requestId);

        if ($request && ($user->getProfile()->getId() == $request->getRequestee()->getId() ||
            $user->getProfile()->getId() == $request->getRequester()->getId())) {
            $this->friendshipRequestRepository->remove($request, true);

            return $this->redirectToRoute('friends_index', ['profileId' => $user->getProfile()->getId()]);
        }
    }

    #[Route('friends/{profileId}', name: 'friends_index')]
    public function index(int $profileId): Response
    {
        $profile = $this->profileRepository->find($profileId);

        if($profile) {
            $friends = []; //Friend list here

            return $this->render('friendship/index.html.twig', [
                'profile' => $profile
            ]);
        }

    }
}
