<?php

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoFormType extends AbstractType
{
    use FormStyle;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // TODO: Change label value
            ->add('image_url', FileType::class, [
                'required' => true,
                'attr' => ['class' => $this->getFileInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]
            ])
            ->add('add_new_photo', SubmitType::class, [
                'label' => 'Submit',
                'attr' => ['class' => $this->getSubmitButtonClass()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
            'attr' => ['class' => $this->getFormClass()]
        ]);
    }
}
