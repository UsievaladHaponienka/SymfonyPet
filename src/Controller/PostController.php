<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use App\Repository\PostRepository;
use App\Service\ImageProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private AlbumRepository $albumRepository;

    private ImageProcessor $imageProcessor;

    private EntityManagerInterface $entityManager;

    private PostRepository $postRepository;
    private PhotoRepository $photoRepository;

    /**
     * @param AlbumRepository $albumRepository
     * @param ImageProcessor $imageProcessor
     * @param PostRepository $postRepository
     * @param PhotoRepository $photoRepository
     */
    public function __construct(
        AlbumRepository $albumRepository,
        ImageProcessor  $imageProcessor,
        PostRepository $postRepository,
        PhotoRepository $photoRepository
    ){
        $this->albumRepository = $albumRepository;
        $this->imageProcessor = $imageProcessor;
        $this->postRepository = $postRepository;
        $this->photoRepository = $photoRepository;
    }

    //TODO: Make it possible to create post in group
    #[Route('/post/create', name: 'post_create', methods: 'POST')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $profile = $user->getProfile();

            $post = new Post();
            $post->setProfile($profile);
            //TODO: Type can also be group, handle it
            $post->setType(Post::USER_POST_TYPE);

            $image = $form->get('photo')->getData();
            $content = $form->get('content')->getData();

            if ($image) {
                $imagePath = $this->imageProcessor
                    ->saveImage($image, ImageProcessor::POST_IMAGE_TYPE);

                /** @var Album $postsAlbum */
                $postsAlbum = $this->albumRepository->findOneBy([
                    'type' => Album::USER_DEFAULT_TYPE,
                    'profile' => $profile->getId()
                ]);
                $photo = new Photo();

                $photo->setAlbum($postsAlbum);
                $photo->setImageUrl('/images/' . $imagePath);
                $photo->setPost($post);
            }

            if ($content) {
                $post->setContent($content);
                if (isset($photo)) {
                    $photo->setDescription($content);
                }
            }

            if ($content || $image) {
                $post->setCreatedAt(new \DateTimeImmutable());
                if (isset($photo)) {
                    $photo->setCreatedAt(new \DateTimeImmutable());
                    $this->photoRepository->save($photo);
                }

                $this->postRepository->save($post, true);

                return $this->redirectToRoute('profile_index', ['profileId' => $profile->getId()]);
            }
        }
        //TODO: Process exception
         throw new \Exception('Post must contain either image or content');
    }

    #[Route('/post/delete/{postId}', name: 'post_delete')]
    public function delete(int $postId): Response
    {
        $post = $this->postRepository->find($postId);

        if($post && $this->isActionAllowed($post)) {
            $profileId = $post->getProfile()->getId();
            $this->postRepository->remove($post, true);

            return $this->redirectToRoute('profile_index', [
                'profileId' => $profileId
            ]);
        }

        throw $this->createNotFoundException();

    }

    protected function isActionAllowed(Post $post): bool
    {
        /** @var User $user */
        $user = $this->getUser();

        return $user->getId() == $post->getProfile()->getUser()->getId();
    }
}
