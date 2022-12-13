<?php

namespace App\Twig\Components;

use App\Entity\Like;
use App\Entity\Profile;
use App\Entity\User;
use App\EntityInterface\LikeableInterface;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('like-button', template: 'components/buttons/like/like-button.html.twig')]
final class LikeButtonComponent
{
    use DefaultActionTrait;

    public const LIKED_BUTTON_STYLE = 'bg-sky-500 hover:bg-sky-400';
    public const NOT_LIKED_BUTTON_STYLE = 'bg-sky-900 hover:bg-sky-800';

    /**
     * @var string $type
     * Defines like type, can be either `post` or `comment`
     */
    public string $type;

    /**
     * @var int $entityId
     * Identifier of related post or comment
     */
    public int $entityId;

    /**
     * @var string $styleClass
     * Defines like button color depending on whether the entity was liked by user
     */
    public string $styleClass;

    /**
     * @var string $buttonText
     */
    public string $buttonText;

    public function __construct(
        private readonly CommentRepository     $commentRepository,
        private readonly PostRepository        $postRepository,
        private readonly TokenStorageInterface $tokenStorage
    )
    {
    }

    public function mount(int $entityId, string $type): void
    {
        $userToken = $this->tokenStorage->getToken();
        if ($userToken) {
            /** @var User $user */
            $user = $userToken->getUser();

            $this->type = $type;
            $this->entityId = $entityId;

            $entityRepository = $type == Like::POST_TYPE ? $this->postRepository : $this->commentRepository;

            /** @var LikeableInterface $entity */
            $entity = $entityRepository->find($entityId);

            $this->styleClass = $entity->isLikedBy($user->getProfile()) ?
                self::LIKED_BUTTON_STYLE :
                self::NOT_LIKED_BUTTON_STYLE;

            $this->buttonText = $this->getLikeButtonText($entity, $user->getProfile());
        }
    }

    public function getLikeButtonText(LikeableInterface $entity, Profile $profile): string
    {
        if ($entity->isLikedBy($profile)) {
            return 'Liked (' . $entity->getLikes()->count() . ')';
        }
        return 'Like (' . $entity->getLikes()->count() . ')';
    }
}
