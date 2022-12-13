<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\User;
use App\EntityInterface\LikeableInterface;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    public function __construct(
        private readonly PostRepository    $postRepository,
        private readonly CommentRepository $commentRepository
    )
    {
    }

    #[Route('like/{entityId}', name: 'like')]
    public function like(Request $request, int $entityId): Response
    {
        $likeType = $request->request->get('type');
        $entityRepository = $likeType == Like::POST_TYPE ? $this->postRepository : $this->commentRepository;

        /** @var LikeableInterface $entity */
        $entity = $entityRepository->find($entityId);

        if ($entity) {
            /** @var User $user */
            $user = $this->getUser();

            if ($entity->isLikedBy($user->getProfile())) {
                $like = $entity
                    ->getLikes()
                    ->filter(function ($element) use ($user) {
                        /** @var Like $element */
                        return $element->getProfile()->getId() == $user->getProfile()->getId();
                    })->first();

                $entity->removeLike($like);
                $entityRepository->save($entity, true);

                return new JsonResponse([
                    'like_added' => false,
                    'button_text' => 'Like (' . $entity->getLikes()->count() . ')'
                ]);
            } else {
                $like = new Like();
                $like->setType($likeType);
                $like->setProfile($user->getProfile());

                $entity->addLike($like);
                $entityRepository->save($entity, true);

                return new JsonResponse([
                    'like_added' => true,
                    'button_text' => 'Liked (' . $entity->getLikes()->count() . ')'
                ]);
            }
        }

        throw $this->createNotFoundException();
    }
}
