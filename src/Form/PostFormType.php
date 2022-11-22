<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    use FormStyle;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => $this->getFileInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])->add('add_new_post', SubmitType::class, [
                'label' => 'Add new post',
                'attr' => ['class' => $this->getSubmitButtonClass()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'attr' => ['class' => $this->getFormClass()] //Add CSS class to form
        ]);
    }
}
