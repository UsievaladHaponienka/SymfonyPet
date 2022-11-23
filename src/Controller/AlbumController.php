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

    #[Route('profile/{profileId}/album/{albumId}', name: 'album_show')]
    public function show(int $profileId, int $albumId): Response
    {
        $album = $this->albumRepository->find($albumId);
        $profile = $this->profileRepository->find($profileId);

        return $this->render('album/show.html.twig', [
            'album' => $album,
            'profile' => $profile
        ]);

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
                'profileId' => $profile->getId(),
                'albumId' => $album->getId()
            ]);
        }

        return $this->render('album/create.html.twig',[
           'albumForm' => $form->createView()
        ]);
    }
}
