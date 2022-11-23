<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
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
    private ImageProcessor $imageProcessor;
    private AlbumRepository $albumRepository;
    private PhotoRepository $photoRepository;

    public function __construct(
        ImageProcessor  $imageProcessor,
        AlbumRepository $albumRepository,
        PhotoRepository $photoRepository
    ){
        $this->imageProcessor = $imageProcessor;
        $this->albumRepository = $albumRepository;
        $this->photoRepository = $photoRepository;
    }

    #[Route('photo/{photoId}', name: 'photo_index')]
    public function index(int $photoId): Response
    {
        $photo = $this->photoRepository->find($photoId);
        if ($photo) {
            return $this->render('photo/index.html.twig', [
                'photo' => $photo
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('album/{albumId}/photo/create', name: 'photo_create')]
    public function create(Request $request, int $albumId): Response
    {
        $album = $this->albumRepository->find($albumId);

        if ($album && $this->isActionAllowed($album)) {
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
        $photo = $this->photoRepository->find($photoId);

        if ($photo && $this->isActionAllowed($photo->getAlbum())) {
            $albumId = $photo->getAlbum()->getId();
            $this->photoRepository->remove($photo, true);

            return $this->redirectToRoute('album_show', ['albumId' => $albumId]);
        }

        throw $this->createNotFoundException();
    }

    protected function isActionAllowed(Album $album): bool
    {
        /** @var User $user */
        $user = $this->getUser();

        return $album->getProfile()->getUser()->getId() == $user->getId();
    }
}
