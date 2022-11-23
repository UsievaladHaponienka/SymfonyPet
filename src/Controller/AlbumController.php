<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\User;
use App\Form\AlbumFormType;
use App\Repository\AlbumRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    private AlbumRepository $albumRepository;
    private ProfileRepository $profileRepository;

    /**
     * @param AlbumRepository $albumRepository
     * @param ProfileRepository $profileRepository
     */
    public function __construct(
        AlbumRepository   $albumRepository,
        ProfileRepository $profileRepository
    ){
        $this->albumRepository = $albumRepository;
        $this->profileRepository = $profileRepository;
    }

    #[Route('profile/{profileId}/albums', name: 'album_index')]
    public function index(int $profileId): Response
    {
        $profile = $this->profileRepository->find($profileId);

        if ($profile) {
            $albums = $this->albumRepository->findBy(['profile' => $profileId]);

            return $this->render('album/index.html.twig', [
                'profile' => $profile,
                'albums' => $albums
            ]);
        }

        //return 404
    }

    //TODO: Check if profile id is actually needed
    #[Route('album/{albumId}', name: 'album_show')]
    public function show(int $albumId): Response
    {
        $album = $this->albumRepository->find($albumId);

        if ($album) {
            $profile = $album->getProfile();

            return $this->render('album/show.html.twig', [
                'album' => $album,
                'profile' => $profile
            ]);
        }

        //return 404
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

        return $this->render('album/create.html.twig',[
           'albumForm' => $form->createView()
        ]);
    }

    #[Route('album/edit/{albumId}', name: 'album_edit')]
    public function edit(Request $request, int $albumId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $album = $this->albumRepository->find($albumId);

        if($album->getProfile()->getUser()->getId() == $user->getId()) {
            $form = $this->createForm(AlbumFormType::class, $album);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('title')->getData() != $album->getTitle()) {
                    $album->setTitle($form->get('title')->getData());
                }
                if ($form->get('description')->getData() != $album->getDescription()) {
                    $album->setDescription($form->get('description')->getData());
                }
                $this->albumRepository->save($album, true);

                return $this->redirectToRoute('album_show', [
                    'albumId' => $album->getId()
                ]);
            }

            return $this->render('album/edit.html.twig',[
                'album' => $album,
                'albumForm' => $form->createView()
            ]);
        }

        // return 404
    }
}
