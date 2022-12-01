<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    private PostRepository $postRepository;

    private LikeRepository $likeRepository;

    public function __construct(
        PostRepository $postRepository,
        LikeRepository $likeRepository
    )
    {
        $this->postRepository = $postRepository;
        $this->likeRepository = $likeRepository;
    }

    #[Route('like/add/{postId}', name: 'like_add')]
    public function addLike(int $postId): Response
    {
        $post = $this->postRepository->find($postId);

        if ($post) {
            /** @var User $user */
            $user = $this->getUser();

            $like = new Like();
            $like->setPost($post);
            $like->setProfile($user->getProfile());

            $this->likeRepository->save($like, true);

            return $this->getRedirect($post);
        }

        throw $this->createNotFoundException();
    }

    #[Route('like/remove/{postId}', name: 'like_remove')]
    public function removeLike(int $postId): Response
    {
        $post = $this->postRepository->find($postId);
        if ($post) {
            /** @var User $user */
            $user = $this->getUser();
            $like = $post
                ->getLikes()
                ->filter(function ($element) use ($user) {
                    /** @var Like $element */
                    return $element->getProfile()->getId() == $user->getProfile()->getId();
                })->first();
            $this->likeRepository->remove($like, true);

            return $this->getRedirect($post);
        }

        throw $this->createNotFoundException();
    }


    //TODO: code duplication with Comment controller and Post controller
    protected function getRedirect(Post $post): Response
    {
        if ($post->getGroup()) {
            return $this->redirectToRoute('group_show', [
                'groupId' => $post->getGroup()->getId(),
                '_fragment' => 'post-' . $post->getId()
            ]);
        } else {
            /** @var User $user */
            $user = $this->getUser();
            return $this->redirectToRoute('profile_index', [
                'profileId' => $user->getProfile()->getId(),
                '_fragment' => 'post-' . $post->getId()
            ]);
        }
    }
}
