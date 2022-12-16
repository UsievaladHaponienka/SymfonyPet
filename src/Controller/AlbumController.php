<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\PrivacySettings;
use App\Entity\User;
use App\Form\AlbumFormType;
use App\Repository\AlbumRepository;
use App\Repository\GroupRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    public function __construct(
        private readonly AlbumRepository   $albumRepository,
        private readonly ProfileRepository $profileRepository,
        private readonly GroupRepository   $groupRepository
    )
    {
    }

    #[Route('albums/profile/{profileId}', name: 'album_profile_index', methods: ['GET'])]
    public function indexProfile(int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->profileRepository->find($profileId);

        if ($profile && $profile->getPrivacySettings()->isViewAllowed(
                PrivacySettings::ALBUMS_CODE, $user->getProfile()
            )) {
            return $this->render('album/index.html.twig', [
                'albums' => $profile->getAlbums(),
                'createUrl' => $this->generateUrl('album_profile_create')
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('albums/group/{groupId}', name: 'album_group_index', methods: ['GET'])]
    public function indexGroup(int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $group->isActionAllowed($user->getProfile(), IEInterface::VIEW_ACTION_CODE)) {
            return $this->render('album/index.html.twig', [
                'albums' => $group->getAlbums(),
                'createUrl' => $this->generateUrl('album_group_create', ['groupId' => $group->getId()])
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/profile/create', name: 'album_profile_create', methods: ['GET', 'POST'])]
    public function createForProfile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AlbumFormType::class);
        $form->handleRequest($request);

        $album = new Album();
        $album->setType(Album::USER_CUSTOM_TYPE);
        $album->setProfile($user->getProfile());

        return $this->create($form, $album);
    }

    #[Route('album/group/create/{groupId}', name: 'album_group_create', methods: ['GET', 'POST'])]
    public function createForGroup(Request $request, int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $group->isAdmin($user->getProfile())) {
            $form = $this->createForm(AlbumFormType::class);
            $form->handleRequest($request);

            $album = new Album();
            $album->setType(Album::GROUP_CUSTOM_TYPE);
            $album->setRelatedGroup($group);

            return $this->create($form, $album);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/edit/{albumId}', name: 'album_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $albumId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $album = $this->albumRepository->find($albumId);

        if ($album && $album->isActionAllowed($user->getProfile())) {
            $form = $this->createForm(AlbumFormType::class, $album);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $album->setTitle($form->get('title')->getData());
                $album->setDescription($form->get('description')->getData());

                $this->albumRepository->save($album, true);

                return $this->redirectToRoute('album_show', ['albumId' => $album->getId()]);
            }

            return $this->render('album/edit.html.twig', [
                'album' => $album,
                'albumForm' => $form->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/{albumId}', name: 'album_show', methods: ['GET'])]
    public function show(int $albumId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $album = $this->albumRepository->find($albumId);

        if ($album && $album->isActionAllowed($user->getProfile(), IEInterface::VIEW_ACTION_CODE)) {
            return $this->render('album/show.html.twig', [
                'album' => $album,
                'backUrl' => $this->getBackUrl($album)
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/delete/{albumId}', name: 'album_delete', methods: ['DELETE'])]
    public function delete(int $albumId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $album = $this->albumRepository->find($albumId);

        if ($album && $album->isActionAllowed($user->getProfile())) {
            $this->albumRepository->remove($album, true);

            return new JsonResponse([
                'backUrl' => $this->getBackUrl($album)
            ]);
        }

        throw $this->createNotFoundException();
    }

    /**
     * Create profile or group album
     *
     * @param FormInterface $form
     * @param Album $album
     * @return Response
     */
    protected function create(FormInterface $form, Album $album): Response
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $album->setTitle($form->get('title')->getData());
            $album->setDescription($form->get('description')->getData());

            $this->albumRepository->save($album, true);

            return $this->redirectToRoute('album_show', ['albumId' => $album->getId()]);
        }

        return $this->render('album/create.html.twig', [
            'albumForm' => $form->createView(),
            'backUrl' => $this->getBackUrl($album)
        ]);
    }

    /**
     * Get url for `Back` button at album show page.
     * If album type is group, button should lead to group show page.
     * If album type is profile, button should lead to profile page.
     *
     * @param Album $album
     * @return string
     */
    protected function getBackUrl(Album $album): string
    {
        if ($album->getType() == Album::GROUP_DEFAULT_TYPE || $album->getType() == Album::GROUP_CUSTOM_TYPE) {
            return $this->generateUrl('album_group_index', [
                'groupId' => $album->getRelatedGroup()->getId()
            ]);
        } else {
            return $this->generateUrl('album_profile_index', [
                'profileId' => $album->getProfile()->getId()
            ]);
        }
    }
}
