<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private ProfileRepository $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request,)
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();
        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

        }

        return $this->render('profile/edit.html.twig', [
            'profileEditForm' => $form->createView(),
            'profile' => $profile
        ]);
    }

    #[Route('/profile/{profileId}', name: 'app_profile')]
    public function index(int $profileId): Response
    {
        $profile = $this->profileRepository->find($profileId);
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', compact('user', 'profile'));
    }


}
