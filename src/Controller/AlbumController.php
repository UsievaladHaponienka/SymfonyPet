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
    )
    {
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
                'user' => $this->getUser(),
                'profile' => $profile,
                'albums' => $albums
            ]);
        }

        //return 404
    }
}
