<?php

namespace App\Controller;

use App\Entity\Comment;
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

    #[Route('comment/create_post/{postId}', name: 'comment_create_post')]
    public function createForPost(Request $request, int $postId): Response
    {
        $post = $this->postRepository->find($postId);

        if ($post) {
            $comment = new Comment();
            $comment->setType(Comment::POST_TYPE);
            $comment->setPost($post);

            return $this->createComment($request, $comment);
        }

        throw $this->createNotFoundException();

    }

    #[Route('comment/create_discussion/{discussionId}', name: 'comment_create_discussion')]
    public function createForDiscussion(Request $request, int $discussionId): Response
    {
        $discussion = $this->discussionRepository->find($discussionId);

        if ($discussion) {
            $comment = new Comment();
            $comment->setType(Comment::DISCUSSION_TYPE);
            $comment->setDiscussion($discussion);

            return $this->createComment($request, $comment);
        }

        throw $this->createNotFoundException();
    }

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

    #[Route('comment/delete/{commentId}', name: 'comment_delete')]
    public function delete(int $commentId): Response
    {
        $comment = $this->commentRepository->find($commentId);

        if ($comment && $this->isActionAllowed($comment)) {
            $this->commentRepository->remove($comment, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
    }

    protected function isActionAllowed(Comment $comment): bool
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user->getProfile()->getId() == $comment->getProfile()->getId();
    }
}
