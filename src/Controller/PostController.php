<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Group;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\AlbumRepository;
use App\Repository\GroupRepository;
use App\Repository\PhotoRepository;
use App\Repository\PostRepository;
use App\Service\ImageProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private AlbumRepository $albumRepository;

    private ImageProcessor $imageProcessor;

    private PostRepository $postRepository;

    private PhotoRepository $photoRepository;

    private GroupRepository $groupRepository;

    /**
     * @param AlbumRepository $albumRepository
     * @param ImageProcessor $imageProcessor
     * @param PostRepository $postRepository
     * @param PhotoRepository $photoRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        AlbumRepository $albumRepository,
        ImageProcessor  $imageProcessor,
        PostRepository  $postRepository,
        PhotoRepository $photoRepository,
        GroupRepository $groupRepository
    )
    {
        $this->albumRepository = $albumRepository;
        $this->imageProcessor = $imageProcessor;
        $this->postRepository = $postRepository;
        $this->photoRepository = $photoRepository;
        $this->groupRepository = $groupRepository;
    }

    #[Route('/post/create-for-user/{profileId}', name: 'post_create_user', methods: 'POST')]
    public function createUserPost(Request $request, int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getProfile()->getId() == $profileId) {
            $postType = Post::USER_POST_TYPE;
            return $this->create($request, $postType);
        }

        throw $this->createNotFoundException();
    }

    #[Route('/post/create-for-group/{groupId}', name: 'post_create_group', methods: 'POST')]
    public function createGroupPost(Request $request, int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $user->getProfile()->getId() == $group->getAdmin()->getId()) {
            $postType = Post::GROUP_POST_TYPE;
            return $this->create($request, $postType, $group);
        }

        throw $this->createNotFoundException();
    }

    protected function create(Request $request, string $postType, $group = null): Response
    {
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $post = new Post();

            if ($postType == Post::USER_POST_TYPE) {
                $post->setType(Post::USER_POST_TYPE);
                $post->setProfile($user->getProfile());
            } else {
                $post->setType(Post::GROUP_POST_TYPE);
                $post->setGroup($group);
            }

            $image = $form->get('photo')->getData();
            $content = $form->get('content')->getData();

            if ($image) {
                $imagePath = $this->imageProcessor
                    ->saveImage($image, ImageProcessor::POST_IMAGE_TYPE);

                $postsAlbum = $this->getPostsAlbum($group);
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

                return $this->getRedirect($group);
            }
        }
        //TODO: Process exception
        throw new \Exception('Post must contain either image or content');
    }

    #[Route('/post/delete/{postId}', name: 'post_delete')]
    public function delete(int $postId): Response
    {
        $post = $this->postRepository->find($postId);

        if ($post && $this->isActionAllowed($post)) {
            $group = $post->getGroup();
            $this->postRepository->remove($post, true);

            return $this->getRedirect($group);
        }

        throw $this->createNotFoundException();

    }

    protected function isActionAllowed(Post $post): bool
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($post->getType() == Post::USER_POST_TYPE) {
            return $user->getProfile()->getId() == $post->getProfile()->getId();
        } else {
            return $user->getProfile()->getId() == $post->getGroup()->getAdmin()->getId();
        }
    }

    //TODO: code duplication with Comment controller (and Likes controller in future)
    protected function getRedirect(Group $group = null): Response
    {
        if ($group) {
            return $this->redirectToRoute('group_show', ['groupId' => $group->getId()]);
        } else {
            /** @var User $user */
            $user = $this->getUser();
            return $this->redirectToRoute('profile_index', ['profileId' => $user->getProfile()->getId()]);
        }
    }

    protected function getPostsAlbum(Group $group = null): ?Album
    {
        if ($group) {
            return $this->albumRepository->findOneBy([
                'type' => Album::GROUP_DEFAULT_TYPE,
                'group' => $group->getId()
            ]);
        } else {
            /** @var User $user */
            $user = $this->getUser();

            return $this->albumRepository->findOneBy([
                'type' => Album::USER_DEFAULT_TYPE,
                'profile' => $user->getProfile()->getId()
            ]);
        }
    }
}
