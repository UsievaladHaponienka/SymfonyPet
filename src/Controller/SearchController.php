<?php

namespace App\Controller;

use App\Form\SearchFormType;
use App\Repository\GroupRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private ProfileRepository $profileRepository;
    private GroupRepository $groupRepository;

    public function __construct(
        ProfileRepository $profileRepository,
        GroupRepository   $groupRepository
    )
    {
        $this->profileRepository = $profileRepository;
        $this->groupRepository = $groupRepository;
    }

    #[Route('search/group', name: 'search_group')]
    public function groupSearch(Request $request)
    {
        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchString = $form->get('search_string')->getData();

            $qb = $this->groupRepository->createQueryBuilder('q');
            $groups = $this->groupRepository
                ->createQueryBuilder('q')
                ->where('q.title LIKE :searchString')
                ->setParameter(':searchString', '%' . $searchString . '%')
                ->getQuery()
                ->getResult();

            return $this->render('group/index.html.twig');


        }
    }
}
