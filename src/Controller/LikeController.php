<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('like/{postId}', name: 'like')]
    public function like(int $postId): Response
    {
        $post = $this->postRepository->find($postId);

        if ($post) {
            /** @var User $user */
            $user = $this->getUser();

            return $post->isLikedBy($user->getProfile()) ?
                $this->removeLike($post, $user->getProfile()) :
                $this->addLike($post, $user->getProfile());
        }

        throw $this->createNotFoundException();
    }

    protected function addLike(Post $post, Profile $profile): Response
    {
        $like = new Like();
        $like->setProfile($profile);
        $post->addLike($like);

        $this->postRepository->save($post, true);

        return new JsonResponse([
            'like_added' => true,
            'button_text' => 'Liked (' .  $post->getLikes()->count() . ')'
        ]);
    }

    protected function removeLike(Post $post, Profile $profile): Response
    {
        $like = $post
            ->getLikes()
            ->filter(function ($element) use ($profile) {
                /** @var Like $element */
                return $element->getProfile()->getId() == $profile->getId();
            })->first();
        $this->likeRepository->remove($like, true);

        return new JsonResponse([
            'like_added' => false,
            'button_text' => 'Like (' .  $post->getLikes()->count() . ')'
        ]);
    }
}
