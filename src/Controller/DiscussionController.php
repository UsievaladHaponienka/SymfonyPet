<?php

namespace App\Controller;

use App\Repository\DiscussionRepository;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/discussion/{groupId}', name: 'discussion_index')]
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
}
