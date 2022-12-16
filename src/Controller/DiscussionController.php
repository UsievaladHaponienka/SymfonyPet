<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\User;
use App\Form\DiscussionFormType;
use App\Repository\DiscussionRepository;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscussionController extends AbstractController
{
    public function __construct(
        private readonly DiscussionRepository $discussionRepository,
        private readonly GroupRepository      $groupRepository
    )
    {
    }

    #[Route('discussions/{groupId}', name: 'discussion_index', methods: ['GET'])]
    public function index(int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $group->isActionAllowed($user->getProfile(), IEInterface::VIEW_ACTION_CODE)) {
            return $this->render('discussion/index.html.twig', [
                'group' => $group,
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('discussion/create/{groupId}', name: 'discussion_create', methods: ['GET', 'POST'])]
    public function create(Request $request, int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $discussionForm = $this->createForm(DiscussionFormType::class);
        $discussionForm->handleRequest($request);

        $group = $this->groupRepository->find($groupId);
        if ($group && $group->isAdmin($user->getProfile())) {
            if ($discussionForm->isSubmitted() && $discussionForm->isValid()) {

                $discussion = new Discussion();
                $discussion->setTitle($discussionForm->get('title')->getData());
                $discussion->setDescription($discussionForm->get('description')->getData());
                $discussion->setRelatedGroup($group);

                $this->discussionRepository->save($discussion, true);

                return $this->redirectToRoute('discussion_index', ['groupId' => $group->getId()]);
            }

            return $this->render('discussion/create.html.twig', [
                'group' => $group,
                'discussionForm' => $discussionForm->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('discussion/{discussionId}', name: 'discussion_show', methods: ['GET'])]
    public function show(int $discussionId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $discussion = $this->discussionRepository->find($discussionId);

        if ($discussion &&
            $discussion->isActionAllowed($user->getProfile(), IEInterface::VIEW_ACTION_CODE)) {
            return $this->render('discussion/show.html.twig', ['discussion' => $discussion]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('discussion/delete/{discussionId}', name: 'discussion_delete', methods: ['DELETE'])]
    public function delete(int $discussionId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $discussion = $this->discussionRepository->find($discussionId);

        if ($discussion && $discussion->isActionAllowed($user->getProfile())) {
            $this->discussionRepository->remove($discussion, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }
}
