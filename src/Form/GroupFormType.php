<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupFormType extends AbstractType
{
    use FormStyle;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Public Group' => 'public',
                    'Private Group' => 'private',
                ],
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('group_image_url', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => $this->getFileInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('create_new_group', SubmitType::class, [
                'label' => 'Submit',
                'attr' => ['class' => $this->getSubmitButtonClass()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
            'attr' => ['class' => $this->getFormClass()]
        ]);
    }
}
