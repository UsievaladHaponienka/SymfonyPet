<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('like/{entityId}', name: 'like')]
    public function like(Request $request, int $entityId): Response
    {
        $likeType = $request->request->get('type');
        /** @var User $user */
        $user = $this->getUser();

        $entityRepository = $likeType == Like::POST_TYPE ?
            $this->entityManager->getRepository(Post::class) :
            $this->entityManager->getRepository(Comment::class);

        $entity = $entityRepository->find($entityId);

        if ($entity && $entity->isActionAllowed($user->getProfile(), IEInterface::VIEW_ACTION_CODE)) {
            if ($entity->getLikeIfExists($user->getProfile())) {
                $entity->removeLike($entity->getLikeIfExists($user->getProfile()));
                $entityRepository->save($entity, true);

                return new JsonResponse([
                    'like_added' => false,
                    'button_text' => 'Like (' . $entity->getLikes()->count() . ')'
                ]);
            } else {
                $like = new Like();
                $like->setProfile($user->getProfile());
                $like->setType($likeType);

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
