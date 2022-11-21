<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private ProfileRepository $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    #[Route('/profile/{profileId}', name: 'app_profile')]
    public function index(int $profileId): Response
    {
        $profile = $this->profileRepository->find($profileId);

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
        ]);
    }
}
