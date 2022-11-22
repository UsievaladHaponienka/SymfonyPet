<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
