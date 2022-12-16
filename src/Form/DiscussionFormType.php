<?php

namespace App\Form;

use App\Entity\Discussion;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscussionFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('discussion_create', SubmitType::class, [
                'label' => 'Submit',
                'attr' => ['class' => $this->getSubmitButtonClass()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discussion::class,
            'attr' => ['class' => $this->getFormClass()]
        ]);
    }
}
