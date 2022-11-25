<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private PostRepository $postRepository;

    private CommentRepository $commentRepository;

    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
    }

    #[Route('comment/create/{postId}', name: 'comment_create')]
    public function create(Request $request, int $postId): Response
    {
        $post = $this->postRepository->find($postId);
        if ($post) {
            $comment = new Comment();
            $form = $this->createForm(CommentFormType::class, $comment);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setContent($form->get('content')->getData());
                $comment->setPost($post);
                $comment->setProfile($this->getUser()->getProfile());

                $this->commentRepository->save($comment, true);
            }

            return $this->redirectToRoute('profile_index', ['profileId' => $post->getProfile()->getId()]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('comment/delete/{commentId}', name: 'comment_delete')]
    public function delete(int $commentId): Response
    {
        $comment = $this->commentRepository->find($commentId);

        if($comment && $this->isActionAllowed($comment)) {
            $profileToRedirect = $comment->getPost()->getProfile()->getId();
            $this->commentRepository->remove($comment, true);

            //TODO: process redirects
            return $this->redirectToRoute('profile_index', ['profileId' => $profileToRedirect]);
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
