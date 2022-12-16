<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search_string', TextType::class, [
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])->add('submit', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['class' => $this->getSubmitButtonClass()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => $this->getFormClass()]
        ]);
    }
}
