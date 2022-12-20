<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\DiscussionRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly PostRepository       $postRepository,
        private readonly DiscussionRepository $discussionRepository,
        private readonly CommentRepository    $commentRepository
    )
    {
    }

    #[Route('comment/create/post/{postId}', name: 'comment_create_post', methods: ['POST'])]
    public function createForPost(Request $request, int $postId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = $this->postRepository->find($postId);

        if ($post && $post->isActionAllowed($user->getProfile(), IEInterface::VIEW_ACTION_CODE)) {
            $comment = new Comment();
            $comment->setType(Comment::POST_TYPE);
            $comment->setPost($post);

            return $this->createComment($request, $comment);
        }

        throw $this->createNotFoundException();
    }

    #[Route('comment/create/discussion/{discussionId}', name: 'comment_create_discussion', methods: ['POST'])]
    public function createForDiscussion(Request $request, int $discussionId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $discussion = $this->discussionRepository->find($discussionId);

        if ($discussion && $discussion->isActionAllowed($user->getProfile(), IEInterface::VIEW_ACTION_CODE)) {
            $comment = new Comment();
            $comment->setType(Comment::DISCUSSION_TYPE);
            $comment->setDiscussion($discussion);

            return $this->createComment($request, $comment);
        }

        throw $this->createNotFoundException();
    }

    #[Route('comment/delete/{commentId}', name: 'comment_delete', methods: ['DELETE'])]
    public function delete(int $commentId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $comment = $this->commentRepository->find($commentId);

        if ($comment && $comment->isActionAllowed($user->getProfile(), IEInterface::DELETE_ACTION_CODE)) {
            $this->commentRepository->remove($comment, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    /**
     * Create post or discussion comment
     *
     * @param Request $request
     * @param Comment $comment
     * @return Response
     */
    protected function createComment(Request $request, Comment $comment): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $comment->setContent($request->request->get('comment_content'));
        $comment->setProfile($user->getProfile());

        $this->commentRepository->save($comment, true);

        return new JsonResponse([
            'commentContent' => $this->renderView('components/comment/comment.html.twig', [
                'comment' => $comment
            ])
        ]);
    }
}
