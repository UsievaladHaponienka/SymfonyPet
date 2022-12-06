<?php

namespace App\Controller\Group;

use App\Entity\Group;
use App\Entity\Invite;
use App\Entity\User;
use App\Form\GroupFormType;
use App\Form\SearchFormType;
use App\Repository\GroupRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\InviteRepository;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupAdminController extends AbstractController
{
    private GroupRequestRepository $groupRequestRepository;
    private GroupRepository $groupRepository;
    private ProfileRepository $profileRepository;
    private ImageProcessor $imageProcessor;
    private SearchService $searchService;
    private InviteRepository $inviteRepository;

    public function __construct(
        GroupRequestRepository $groupRequestRepository,
        GroupRepository        $groupRepository,
        ProfileRepository      $profileRepository,
        InviteRepository       $inviteRepository,
        ImageProcessor         $imageProcessor,
        SearchService          $searchService
    )
    {
        $this->groupRequestRepository = $groupRequestRepository;
        $this->groupRepository = $groupRepository;
        $this->profileRepository = $profileRepository;
        $this->imageProcessor = $imageProcessor;
        $this->searchService = $searchService;
        $this->inviteRepository = $inviteRepository;
    }

    #[Route('group/edit/{groupId}', name: 'group_edit')]
    public function edit(Request $request, int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group && $this->isAdmin($group)) {
            $groupEditForm = $this->createForm(GroupFormType::class, $group);
            $groupEditForm->handleRequest($request);

            if ($groupEditForm->isSubmitted() && $groupEditForm->isValid()) {
                $this->editGroup($group, $groupEditForm);

                return $this->redirectToRoute('group_show', ['groupId' => $group->getId()]);
            }

            $profileSearchForm = $this->createForm(SearchFormType::class);
            $profileSearchForm->handleRequest($request);


            $profileSearchResult = null;
            if ($profileSearchForm->isSubmitted() && $profileSearchForm->isValid()) {
                $profileSearchResult = $this->searchService->searchProfiles(
                    $profileSearchForm->get('search_string')->getData()
                );
            }

            return $this->render('group/edit.html.twig', [
                'group' => $group,
                'groupEditForm' => $groupEditForm->createView(),
                'profileSearchForm' => $profileSearchForm->createView(),
                'profileSearchResult' => $profileSearchResult
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/delete/{groupId}', name: 'group_delete', methods: ['DELETE'])]
    public function delete(int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group && $this->isAdmin($group)) {
            $groupTitle = $group->getTitle();
            $this->groupRepository->remove($group, true);

            return new JsonResponse(['groupTitle' => $groupTitle]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/request/decline/{requestId}', name: 'group_request_decline')]
    public function declineJoinRequest(int $requestId): Response
    {
        $joinRequest = $this->groupRequestRepository->find($requestId);

        if ($joinRequest && $this->isAdmin($joinRequest->getRelatedGroup())) {
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

                return new JsonResponse(['username' => $profile->getUsername()]);
            }

            throw $this->createNotFoundException();
        }

        throw $this->createNotFoundException();
    }

    #[Route('invite/create/{profileId}/{groupId}', name: 'invite_create')]
    public function createInvite(int $profileId, int $groupId): Response
    {
        $profile = $this->profileRepository->find($profileId);
        $group = $this->groupRepository->find($groupId);

        if ($profile && $group && $this->isAdmin($group)) {
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

        if ($invite && ($this->isAdmin($invite->getRelatedGroup()) ||
                $user->getProfile()->getId() == $invite->getProfile()->getId())) {
            $this->inviteRepository->remove($invite, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    protected function editGroup(Group $group, FormInterface $groupEditForm): void
    {
        $group->setTitle($groupEditForm->get('title')->getData());
        $group->setDescription($groupEditForm->get('description')->getData());
        $group->setType($groupEditForm->get('type')->getData());

        $image = $groupEditForm->get('group_image_url')->getData();
        if ($image) {
            $newFileName = $this->imageProcessor->saveImage(
                $image,
                ImageProcessor::PROFILE_IMAGE_TYPE,
                '/public/images/group/'
            );
            $group->setGroupImageUrl('/images/group/' . $newFileName);
        }

        $this->groupRepository->save($group, true);
    }

    protected function isAdmin(Group $group): bool
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user->getProfile()->getId() == $group->getAdmin()->getId();
    }
}
