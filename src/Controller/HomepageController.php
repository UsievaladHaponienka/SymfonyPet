<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user) {
            return $this->redirectToRoute('profile_index', ['profileId' => $user->getProfile()->getId()]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
