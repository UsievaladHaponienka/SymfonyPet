<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    private GroupRepository $groupRepository;
    private ProfileRepository $profileRepository;

    public function __construct(
        GroupRepository $groupRepository,
        ProfileRepository $profileRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->profileRepository = $profileRepository;
    }

    #[Route('/groups/{profileId}', name: 'group_index')]
    public function index(int $profileId): Response
    {
        $profile = $this->profileRepository->find($profileId);
        $groups = $profile->getGroups();

        return $this->render('group/index.html.twig', [
            'groups' => $groups,
        ]);
    }
}
