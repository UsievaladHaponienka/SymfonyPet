<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Entity\PrivacySettings;
use App\Entity\User;
use App\Form\PhotoFormType;
use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use App\Service\ImageProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    public function __construct(
        private readonly ImageProcessor  $imageProcessor,
        private readonly AlbumRepository $albumRepository,
        private readonly PhotoRepository $photoRepository
    )
    {
    }

    #[Route('photo/{photoId}', name: 'photo_index')]
    public function index(int $photoId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $photo = $this->photoRepository->find($photoId);

        if ($photo && $photo->getAlbum()->isViewAllowed($user->getProfile())) {
            return $this->render('photo/index.html.twig', [
                'photo' => $photo
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/{albumId}/photo/create', name: 'photo_create')]
    public function create(Request $request, int $albumId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $album = $this->albumRepository->find($albumId);

        if ($album && $album->isActionAllowed($user->getProfile())) {
            $photo = new Photo();
            $form = $this->createForm(PhotoFormType::class, $photo);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $image = $form->get('image_url')->getData();
                $description = $form->get('description')->getData();

                $imagePath = $this->imageProcessor->saveImage(
                    $image,
                    ImageProcessor::PHOTO_IMAGE_TYPE,

                );

                $photo->setAlbum($album);
                $photo->setImageUrl('/images/' . $imagePath);
                $photo->setCreatedAt(new \DateTimeImmutable());

                if ($description) {
                    $photo->setDescription($description);
                }

                $this->photoRepository->save($photo, true);

                return $this->redirectToRoute('album_show', ['albumId' => $albumId]);
            }

            return $this->render('photo/create.html.twig', [
                'album' => $album,
                'photoForm' => $form->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('photo/delete/{photoId}', name: 'photo_delete')]
    public function delete(int $photoId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $photo = $this->photoRepository->find($photoId);

        if ($photo && $photo->isActionAllowed($user->getProfile())) {
            $albumId = $photo->getAlbum()->getId();
            $this->photoRepository->remove($photo, true);

            return $this->redirectToRoute('album_show', ['albumId' => $albumId]);
        }

        throw $this->createNotFoundException();
    }
}
