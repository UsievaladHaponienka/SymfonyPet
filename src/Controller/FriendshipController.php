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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    private ProfileRepository $profileRepository;
    private FriendshipRequestRepository $friendshipRequestRepository;
    private FriendshipRepository $friendshipRepository;
    private SearchService $searchService;

    public function __construct(
        ProfileRepository           $profileRepository,
        FriendshipRequestRepository $friendshipRequestRepository,
        FriendshipRepository        $friendshipRepository,
        SearchService               $searchService
    )
    {
        $this->profileRepository = $profileRepository;
        $this->friendshipRequestRepository = $friendshipRequestRepository;
        $this->friendshipRepository = $friendshipRepository;
        $this->searchService = $searchService;
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

            return $this->redirectToRoute('friends_index');
        }

        throw $this->createNotFoundException();
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

            return $this->redirectToRoute('friends_index');
        }

        throw $this->createNotFoundException();
    }

    #[Route('friendship/delete/{friendshipId}', name: 'friendship_delete')]
    public function deleteFriendship(int $friendshipId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $firstFriendshipObject = $this->friendshipRepository->find($friendshipId);

        if ($firstFriendshipObject->getProfile()->getId() == $user->getProfile()->getId()) {

            $this->friendshipRepository->remove($firstFriendshipObject);

            $secondFriendshipObject = $this->friendshipRepository->findOneBy([
                'profile' => $firstFriendshipObject->getFriend()->getId(),
                'friend' => $user->getProfile()->getId()
            ]);
            $this->friendshipRepository->remove($secondFriendshipObject, true);

            return $this->redirectToRoute('friends_index');
        }

        throw $this->createNotFoundException();
    }
}
