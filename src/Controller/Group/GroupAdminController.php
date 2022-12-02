<?php

namespace App\Controller\Group;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupFormType;
use App\Repository\GroupRepository;
use App\Repository\GroupRequestRepository;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(
        GroupRequestRepository $groupRequestRepository,
        GroupRepository $groupRepository,
        ProfileRepository $profileRepository,
        ImageProcessor $imageProcessor
    )
    {
        $this->groupRequestRepository = $groupRequestRepository;
        $this->groupRepository = $groupRepository;
        $this->profileRepository = $profileRepository;
        $this->imageProcessor = $imageProcessor;
    }

    #[Route('group/edit/{groupId}', name: 'group_edit')]
    public function edit(Request $request, int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group && $this->isAdmin($group)) {
            $form = $this->createForm(GroupFormType::class, $group);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $group->setTitle($form->get('title')->getData());
                $group->setDescription($form->get('description')->getData());
                $group->setType($form->get('type')->getData());

                $image = $form->get('group_image_url')->getData();
                if ($image) {
                    $newFileName = $this->imageProcessor->saveImage(
                        $image,
                        ImageProcessor::PROFILE_IMAGE_TYPE,
                        '/public/images/group/'
                    );
                    $group->setGroupImageUrl('/images/group/' . $newFileName);
                }

                $this->groupRepository->save($group, true);

                return $this->redirectToRoute('group_show', ['groupId' => $group->getId()]);
            }

            return $this->render('group/edit.html.twig', [
                'group' => $group,
                'groupEditForm' => $form->createView()
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
