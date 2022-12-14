<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\FriendshipRequest;
use App\Entity\User;
use App\Form\SearchFormType;
use App\Repository\FriendshipRepository;
use App\Repository\FriendshipRequestRepository;
use App\Repository\ProfileRepository;
use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    public function __construct(
        private readonly ProfileRepository           $profileRepository,
        private readonly FriendshipRequestRepository $friendshipRequestRepository,
        private readonly FriendshipRepository        $friendshipRepository,
        private readonly SearchService               $searchService
    )
    {
    }

    #[Route('friends', name: 'friends_index')]
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $profileSearchForm = $this->createForm(SearchFormType::class);
        $profileSearchForm->handleRequest($request);

        $profileSearchResult = null;
        if ($profileSearchForm->isSubmitted() && $profileSearchForm->isValid()) {
            $profileSearchResult = $this->searchService->searchProfiles(
                $profileSearchForm->get('search_string')->getData()
            );
        }

        return $this->render('friendship/index.html.twig', [
            'profile' => $profile,
            'searchForm' => $profileSearchForm->createView(),
            'profileSearchResult' => $profileSearchResult
        ]);
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

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship-request/delete/{profileId}', name: 'friendship_request_delete')]
    public function deleteRequest(int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $request = $this->friendshipRequestRepository->findOneBy([
            'requestee' => [$user->getProfile()->getId(), $profileId],
            'requester' => [$user->getProfile()->getId(), $profileId]
        ]);

        if ($request) {
            $this->friendshipRequestRepository->remove($request, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship/create/{profileId}', name: 'friendship_create')]
    public function createFriendship(int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $friendProfile = $this->profileRepository->find($profileId);

        if ($profile && $friendProfile) {
            $friendshipObjectForFirstUser = new Friendship();

            $friendshipObjectForFirstUser->setProfile($profile);
            $friendshipObjectForFirstUser->setFriend($friendProfile);

            $friendshipObjectForSecondUser = new Friendship();

            $friendshipObjectForSecondUser->setProfile($friendProfile);
            $friendshipObjectForSecondUser->setFriend($profile);

            $this->friendshipRepository->save($friendshipObjectForFirstUser);
            $this->friendshipRepository->save($friendshipObjectForSecondUser);

            $request = $this->friendshipRequestRepository->findOneBy([
                'requester' => $friendProfile->getId(),
                'requestee' => $profile->getId()
            ]);

            $this->friendshipRequestRepository->remove($request, true);

            return new JsonResponse(['username' => $friendProfile->getUsername()]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship/delete/{profileId}', name: 'friendship_delete')]
    public function deleteFriendship(int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $friendshipObjects = $this->friendshipRepository->findBy([
            'profile' => [$user->getProfile()->getId(), $profileId],
            'friend' => [$user->getProfile()->getId(), $profileId]
        ]);

        if ($friendshipObjects) {
            foreach ($friendshipObjects as $friendship) {
                $this->friendshipRepository->remove($friendship, true);
            }

            return new JsonResponse(['username' => $this->profileRepository->find($profileId)->getUsername()]);
        }

        throw $this->createNotFoundException();
    }
}
