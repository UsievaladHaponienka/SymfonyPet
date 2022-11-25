<?php

namespace App\Twig\Components;

use App\Entity\Comment;
use App\Form\CommentFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('post_comment')]
final class PostCommentComponent
{
    public int $postId;

    public $result;

    private FormFactoryInterface $formFactory;
    private RouterInterface $router;

    public FormView $form;

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function mount(int $postId)
    {
        $comment = new Comment();
        $this->form = $this->formFactory->create(CommentFormType::class, $comment, [
            'method' => 'POST',
            'action' => $this->router->generate('comment_create', ['postId' => $postId]),
        ])->createView();
    }
}
