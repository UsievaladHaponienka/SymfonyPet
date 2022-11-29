<?php

namespace App\Twig\Components;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\PostRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('post/post-comment')]
final class PostCommentComponent
{
    private FormFactoryInterface $formFactory;

    private RouterInterface $router;

    public FormView $form;

    private PostRepository $postRepository;

    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        PostRepository $postRepository
    ){
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->postRepository = $postRepository;
    }

    public function mount(int $postId)
    {
        $post = $this->postRepository->find($postId);

        if($post) {
            $comment = new Comment();
            $comment->setPost($post);

            $this->form = $this->formFactory->create(CommentFormType::class, $comment, [
                'method' => 'POST',
                'action' => $this->router->generate('comment_create', ['postId' => $postId]),
            ])->createView();
        }
    }
}
