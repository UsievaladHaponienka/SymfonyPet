<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\PrivacySettings;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $authenticator,
        LoginFormAuthenticator $formAuthenticator

    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $profile = new Profile();
            $defaultUserAlbum = new Album();

            $defaultUserAlbum->setType(Album::USER_DEFAULT_TYPE);
            $defaultUserAlbum->setTitle(Album::DEFAULT_ALBUM_TITLE);

            $user->setProfile($profile);
            $entityManager->persist($user);

            $defaultUserAlbum->setProfile($profile);
            $entityManager->persist($profile);

            $entityManager->persist($defaultUserAlbum);

            $profilePrivacySettings = new PrivacySettings();
            $profilePrivacySettings->setProfile($profile);
            $entityManager->persist($profilePrivacySettings);

            $entityManager->flush();

            // Log in user after registration
            return $authenticator->authenticateUser($user, $formAuthenticator, $request);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
