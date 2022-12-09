<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PostFormType;
use App\Form\ProfileFormType;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly ProfileRepository    $profileRepository,
        private readonly ImageProcessor       $imageProcessor,
    )
    {
    }

    #[Route('/profile/edit', name: 'profile_edit')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('username')->getData()) {
                $profile->setUsername($form->get('username')->getData());
            }

            if ($form->get('description')->getData()) {
                $profile->setDescription($form->get('description')->getData());
            }

            $image = $form->get('profile_image_url')->getData();
            if ($image) {
                $newFileName = $this->imageProcessor->saveImage(
                    $image,
                    ImageProcessor::PROFILE_IMAGE_TYPE,
                    '/public/images/profile/'
                );
                $profile->setProfileImageUrl('/images/profile/' . $newFileName);
            }

            $this->profileRepository->save($profile, true);
            return $this->redirectToRoute('profile_index', [
                'profileId' => $profile->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'profileEditForm' => $form->createView(),
            'profile' => $profile
        ]);
    }

    #[Route('/profile/{profileId}', name: 'profile_index')]
    public function index(Request $request, int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->profileRepository->find($profileId);

        if ($profile) {
            $postForm = $this->createForm(
                PostFormType::class, null, [
                'action' => $this->generateUrl('post_create_user', ['profileId' => $profileId]),
                'method' => 'POST'
            ]);

            return $this->render('profile/index.html.twig', [
                'profile' => $profile,
                'postForm' => $postForm->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }
}
