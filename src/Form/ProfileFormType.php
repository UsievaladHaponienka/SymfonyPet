<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'required' => false,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('profile_image_url', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => $this->getFileInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])->add('editProfile', SubmitType::class, [
                'label' => 'Edit Profile',
                'attr' => ['class' => $this->getSubmitButtonClass()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
            'attr' => ['class' => $this->getFormClass()] //Add CSS class to form
        ]);
    }
}
