<?php

namespace App\Controller;

use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Repository\AlbumRepository;
use App\Repository\GroupRepository;
use App\Repository\PostRepository;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(
        private readonly ImageProcessor    $imageProcessor,
        private readonly PostRepository    $postRepository,
        private readonly GroupRepository   $groupRepository,
        private readonly ProfileRepository $profileRepository,
        private readonly AlbumRepository   $albumRepository
    )
    {
    }

    #[Route('post/create/user/{profileId}', name: 'post_create_user', methods: 'POST')]
    public function createUserPost(Request $request, int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->profileRepository->find($profileId);

        if ($profile && $user->getProfile()->getId() == $profileId) {
            $post = new Post();
            $post->setProfile($profile);
            $post->setType(Post::USER_POST_TYPE);
            return $this->create($request, $post);
        }

        throw $this->createNotFoundException();
    }

    #[Route('post/create/group/{groupId}', name: 'post_create_group', methods: 'POST')]
    public function createGroupPost(Request $request, int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $group->isAdmin($user->getProfile())) {
            $post = new Post();
            $post->setRelatedGroup($group);
            $post->setType(Post::GROUP_POST_TYPE);
            return $this->create($request, $post);
        }

        throw $this->createNotFoundException();
    }

    protected function create(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('photo')->getData();
            $content = $form->get('content')->getData();

            if (!$image && !$content) {
                $this->addFlash('post-failure', 'Post must contain either image or content');
                return $this->getRedirect($post);
            }

            if ($image) {
                $imagePath = $this->imageProcessor->saveImage($image, ImageProcessor::POST_IMAGE_TYPE);

                $photo = new Photo();
                $postsAlbum = $post->getType() == Post::USER_POST_TYPE ?
                    $post->getProfile()->getDefaultAlbum() :
                    $post->getRelatedGroup()->getDefaultAlbum();

                $photo->setAlbum($postsAlbum);
                $photo->setPost($post);
                $photo->setImageUrl('/images/' . $imagePath);
                $photo->setCreatedAt(new \DateTimeImmutable());

                if ($content) {
                    $photo->setDescription($content);
                }

                $postsAlbum->addPhoto($photo);
                $this->albumRepository->save($postsAlbum);
            }

            if ($content) {
                $post->setContent($content);
            }

            $post->setCreatedAt(new \DateTimeImmutable());
            $this->postRepository->save($post, true);

            return $this->getRedirect($post);
        }

        throw $this->createNotFoundException();
    }

    #[Route('/post/delete/{postId}', name: 'post_delete')]
    public function delete(int $postId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = $this->postRepository->find($postId);

        if ($post && $post->isActionAllowed($user->getProfile(), IEInterface::DELETE_ACTION_CODE)) {
            $this->postRepository->remove($post, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    /**
     * Get redirect url which is used after post is created.
     * If post type = profile, redirects to profile index page.
     * If post type = group, redirects to group show page.
     *
     * @param Post $post
     * @return Response
     */
    protected function getRedirect(Post $post): Response
    {
        return $post->getType() == Post::USER_POST_TYPE ?
            $this->redirectToRoute('profile_index', ['profileId' => $post->getProfile()->getId()]) :
            $this->redirectToRoute('group_show', ['groupId' => $post->getRelatedGroup()->getId()]);
    }
}
