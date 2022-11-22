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
    /**
     * @var ProfileRepository
     */
    private ProfileRepository $profileRepository;

    private ImageProcessor $imageProcessor;

    /**
     * @param ProfileRepository $profileRepository
     * @param ImageProcessor $imageProcessor
     */
    public function __construct(
        ProfileRepository $profileRepository,
        ImageProcessor $imageProcessor
    ) {
        $this->profileRepository = $profileRepository;
        $this->imageProcessor = $imageProcessor;
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
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
                $newFileName = $this->imageProcessor->saveImage($image, '/public/images/profile');
                $profile->setProfileImageUrl('/images/profile/' . $newFileName);
            }

            $this->profileRepository->save($profile, true);
        }

        return $this->render('profile/edit.html.twig', [
            'profileEditForm' => $form->createView(),
            'user' => $user,
            'profile' => $profile
        ]);
    }

    #[Route('/profile/{profileId}', name: 'app_profile')]
    public function index(int $profileId): Response
    {
        $user = $this->getUser();
        $profile = $this->profileRepository->find($profileId);
        $posts = $profile->getPosts();
        $postForm = $this->createForm(PostFormType::class, null, [
            'action' => $this->generateUrl('post_create'),
            'method' => 'POST'
        ]);

        foreach ($posts->getIterator() as $post) {
            $a = 1;
            $b = 2;
        }

        return $this->render('profile/index.html.twig',
            [
                'user' => $user,
                'profile' => $profile,
                'posts' => $posts,
                'postForm' => $postForm->createView()
            ]);
    }
}
