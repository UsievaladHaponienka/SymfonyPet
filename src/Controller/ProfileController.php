<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Form\ProfileFormType;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private ProfileRepository $profileRepository;

    private ImageProcessor $imageProcessor;

    /**
     * @param ProfileRepository $profileRepository
     * @param ImageProcessor $imageProcessor
     */
    public function __construct(
        ProfileRepository $profileRepository,
        ImageProcessor    $imageProcessor
    )
    {
        $this->profileRepository = $profileRepository;
        $this->imageProcessor = $imageProcessor;
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
    public function index(int $profileId): Response
    {
        $profile = $this->profileRepository->find($profileId);

        $post = new Post();
        $postForm = $this->createForm(PostFormType::class, $post, [
            'action' => $this->generateUrl('post_create_user', ['profileId' => $profileId]),
            'method' => 'POST'
        ]);

        return $this->render('profile/index.html.twig',
            [
                'profile' => $profile,
                'postForm' => $postForm->createView()
            ]);
    }
}
