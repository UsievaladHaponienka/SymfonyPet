<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\PrivacySettings;
use App\Entity\User;
use App\Form\AlbumFormType;
use App\Repository\AlbumRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    public function __construct(
        private readonly AlbumRepository   $albumRepository,
        private readonly ProfileRepository $profileRepository
    )
    {
    }

    #[Route('/albums/{profileId}', name: 'album_index')]
    public function index(int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->profileRepository->find($profileId);

        if ($profile && $profile->getPrivacySettings()->isAccessAllowed(
            PrivacySettings::ALBUMS_CODE, $user->getProfile()
            )) {
            return $this->render('album/index.html.twig', [
                'profile' => $profile
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/create', name: 'album_create')]
    public function create(Request $request): Response
    {
        $album = new Album();

        $form = $this->createForm(AlbumFormType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $profile = $user->getProfile();

            $album->setProfile($profile);
            $album->setTitle($form->get('title')->getData());
            $album->setType(Album::USER_CUSTOM_TYPE);

            if ($form->get('description')->getData()) {
                $album->setDescription($form->get('description')->getData());
            }

            $this->albumRepository->save($album, true);

            return $this->redirectToRoute('album_show', [
                'albumId' => $album->getId()
            ]);
        }

        return $this->render('album/create.html.twig', [
            'albumForm' => $form->createView()
        ]);
    }

    #[Route('album/edit/{albumId}', name: 'album_edit')]
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

                return $this->redirectToRoute('album_show', [
                    'albumId' => $album->getId()
                ]);
            }

            return $this->render('album/edit.html.twig', [
                'album' => $album,
                'albumForm' => $form->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/{albumId}', name: 'album_show')]
    public function show(int $albumId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $album = $this->albumRepository->find($albumId);

        //TODO:: implement for group
        if ($album && $album->getProfile()->getPrivacySettings()->isAccessAllowed(
                PrivacySettings::ALBUMS_CODE, $user->getProfile()
            )) {
            return $this->render('album/show.html.twig', [
                'album' => $album,
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/delete/{albumId}', name: 'album_delete')]
    public function delete(int $albumId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $album = $this->albumRepository->find($albumId);

        if ($album && $album->isActionAllowed($user->getProfile())) {
            $this->albumRepository->remove($album, true);

            return new JsonResponse([
                'redirectUrl' => $this->generateUrl('album_index', ['profileId' => $album->getProfile()->getId()])
            ]);
        }

        throw $this->createNotFoundException();
    }
}
