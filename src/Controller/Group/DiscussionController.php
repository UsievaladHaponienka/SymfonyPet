<?php

namespace App\Controller\Group;

use App\Entity\Discussion;
use App\Entity\User;
use App\Form\DiscussionFormType;
use App\Repository\DiscussionRepository;
use App\Repository\GroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscussionController extends BaseGroupController
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
        $group = $this->groupRepository->find($groupId);
         if($group) {
             return $this->render('discussion/index.html.twig', [
                 'group' => $group,
             ]);
         }

         throw $this->createNotFoundException();
    }

    #[Route('discussion/create/{groupId}', name: 'discussion_create')]
    public function create(Request $request, int $groupId): Response
    {
        $discussionForm = $this->createForm(DiscussionFormType::class);
        $discussionForm->handleRequest($request);

        $group = $this->groupRepository->find($groupId);
        if($group && $this->isAdmin($group)) {
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

            if ($group->isPublic() || $group->isInGroup($user->getProfile())) {

                return $this->render('discussion/show.html.twig', [
                    'group' => $group,
                    'discussion' => $discussion
                ]);
            }

            throw $this->createNotFoundException();
        }

        throw $this->createNotFoundException();
    }
}
