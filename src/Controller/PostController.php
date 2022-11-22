<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\AlbumRepository;
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

    /**
     * @param AlbumRepository $albumRepository
     * @param ImageProcessor $imageProcessor
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AlbumRepository $albumRepository,
        ImageProcessor  $imageProcessor,
        EntityManagerInterface $entityManager
    ){
        $this->albumRepository = $albumRepository;
        $this->imageProcessor = $imageProcessor;
        $this->entityManager = $entityManager;
    }

    //TODO: Make it possible to create post in group
    #[Route('/post/create', name: 'post_create', methods: 'POST')]
    public function create(Request $request)
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
                $imagePath = $this->imageProcessor->saveImage($image);

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
                $this->entityManager->persist($post);
                if (isset($photo)) {
                    $photo->setCreatedAt(new \DateTimeImmutable());
                    $this->entityManager->persist($photo);
                }

                $this->entityManager->flush();
                return $this->redirectToRoute('app_profile', ['profileId' => $profile->getId()]);
            }
        }
        //TODO: Process exception
         throw new \Exception('Post must contain either image or content');
    }

    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
