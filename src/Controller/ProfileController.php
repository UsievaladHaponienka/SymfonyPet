<?php

namespace App\Controller;

use App\Entity\PrivacySettings;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\PostFormType;
use App\Form\PrivacySettingsFormType;
use App\Form\ProfileFormType;
use App\Repository\PrivacySettingsRepository;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly ProfileRepository         $profileRepository,
        private readonly PrivacySettingsRepository $privacySettingsRepository,
        private readonly ImageProcessor            $imageProcessor,
    )
    {
    }

    #[Route('/profile/edit', name: 'profile_edit')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $profileEditForm = $this->createForm(ProfileFormType::class, $profile);
        $profileEditForm->handleRequest($request);

        $privacySettingForm = $this->createForm(PrivacySettingsFormType::class, $profile->getPrivacySettings());
        $privacySettingForm->handleRequest($request);

        if ($profileEditForm->isSubmitted() && $profileEditForm->isValid()) {
            return $this->editProfile($profileEditForm, $profile);
        }

        if ($privacySettingForm->isSubmitted() && $privacySettingForm->isValid()) {
            return $this->editPrivacySettings($privacySettingForm, $profile);
        }

        return $this->render('profile/edit.html.twig', [
            'profileEditForm' => $profileEditForm->createView(),
            'privacySettingsForm' => $privacySettingForm->createView(),
            'profile' => $profile
        ]);
    }

    #[Route('/profile/{profileId}', name: 'profile_index')]
    public function index(int $profileId): Response
    {
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

    /**
     * Process Profile edit form and save changes.
     *
     * @param FormInterface $profileEditForm
     * @param Profile $profile
     * @return Response
     */
    protected function editProfile(FormInterface $profileEditForm, Profile $profile): Response
    {
        if ($profileEditForm->get('username')->getData()) {
            $profile->setUsername($profileEditForm->get('username')->getData());
        }

        if ($profileEditForm->get('description')->getData()) {
            $profile->setDescription($profileEditForm->get('description')->getData());
        }

        $image = $profileEditForm->get('profile_image_url')->getData();
        if ($image) {
            $newFileName = $this->imageProcessor->saveImage(
                $image,
                ImageProcessor::PROFILE_IMAGE_TYPE,
                '/public/images/profile/'
            );
            $profile->setProfileImageUrl('/images/profile/' . $newFileName);
        }

        $this->profileRepository->save($profile, true);

        $this->addFlash('profile-edit', 'Profile updated');
        return $this->redirectToRoute('profile_edit');
    }

    /**
     * Process Profile Privacy Settings form and save changes.
     *
     * @param FormInterface $privacySettingForm
     * @param Profile $profile
     * @return Response
     */
    protected function editPrivacySettings(FormInterface $privacySettingForm, Profile $profile): Response
    {
        $settings = $profile->getPrivacySettings();

        $settings->setFriendList($privacySettingForm->get(PrivacySettings::FRIEND_LIST_CODE)->getData());
        $settings->setGroupList($privacySettingForm->get(PrivacySettings::GROUPS_LIST_CODE)->getData());
        $settings->setAlbums($privacySettingForm->get(PrivacySettings::ALBUMS_CODE)->getData());
        $settings->setPosts($privacySettingForm->get(PrivacySettings::POSTS_CODE)->getData());

        $this->privacySettingsRepository->save($settings, true);

        $this->addFlash('profile-edit', 'Privacy settings updated');

        return $this->redirectToRoute('profile_edit');
    }
}
