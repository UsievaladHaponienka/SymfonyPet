<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository
    )
    {}

    #[Route('comment/create/{postId}', name: 'comment_create')]
    public function create(Request $request, int $postId): Response
    {
        $post = $this->postRepository->find($postId);
        if ($post) {
            /** @var User $user */
            $user = $this->getUser();
            $comment = new Comment();
            $comment->setContent($request->request->get('comment_content'));
            $comment->setProfile($user->getProfile());

            $post->addComment($comment);

            $this->postRepository->save($post, true);

            return new JsonResponse();
        }

        throw $this->createNotFoundException();
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
