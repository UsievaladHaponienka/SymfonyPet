<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @var ProfileRepository
     */
    private ProfileRepository $profileRepository;

    /**
     * @param ProfileRepository $profileRepository
     */
    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();
        $form = $this->createForm(ProfileFormType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if($form->get('username')->getData()) {
                $profile->setUsername($form->get('username')->getData());
            }

            if($form->get('description')->getData()) {
                $profile->setDescription($form->get('description')->getData());
            }

            //TODO: Resize image
            $imagePath = $form->get('profile_image_url')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/images',
                        $newFileName);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $profile->setProfileImageUrl('/images/' . $newFileName);
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
        $profile = $this->profileRepository->find($profileId);
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', compact('user', 'profile'));
    }
}
