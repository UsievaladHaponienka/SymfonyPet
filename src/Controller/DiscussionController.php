<?php

namespace App\Controller;

use App\Entity\Discussion;
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
        private readonly GroupRepository $groupRepository
    )
    {
    }

    #[Route('/discussions/{groupId}', name: 'discussion_index')]
    public function index(int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

         if($group && $group->isViewAllowed($user->getProfile())) {
             return $this->render('discussion/index.html.twig', [
                 'group' => $group,
             ]);
         }

         throw $this->createNotFoundException();
    }

    #[Route('discussion/create/{groupId}', name: 'discussion_create')]
    public function create(Request $request, int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $discussionForm = $this->createForm(DiscussionFormType::class);
        $discussionForm->handleRequest($request);

        $group = $this->groupRepository->find($groupId);
        if($group && $group->isAdmin($user->getProfile())) {
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

    #[Route('discussion/{discussionId}', name: 'discussion_show')]
    public function show(int $discussionId): Response
    {
        $discussion = $this->discussionRepository->find($discussionId);

        if ($discussion) {
            /** @var User $user */
            $user = $this->getUser();
            $group = $discussion->getRelatedGroup();

            if ($group->isViewAllowed($user->getProfile())) {
                return $this->render('discussion/show.html.twig', [
                    'group' => $group,
                    'discussion' => $discussion
                ]);
            }

            throw $this->createNotFoundException();
        }

        throw $this->createNotFoundException();
    }

    #[Route('discussion/delete/{discussionId}', name: 'discussion_delete')]
    public function delete(int $discussionId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $discussion = $this->discussionRepository->find($discussionId);

        if($discussion && $discussion->getRelatedGroup()->isAdmin($user->getProfile())) {
            $this->discussionRepository->remove($discussion, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }
}
