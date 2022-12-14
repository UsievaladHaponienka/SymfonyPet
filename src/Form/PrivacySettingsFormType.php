<?php

namespace App\Form;

use App\Entity\PrivacySettings;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivacySettingsFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(PrivacySettings::FRIEND_LIST_CODE, ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]])
            ->add(PrivacySettings::GROUPS_LIST_CODE, ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]])
            ->add(PrivacySettings::ALBUMS_CODE, ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]])
            ->add(PrivacySettings::POSTS_CODE, ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit changes',
                'attr' => ['class' => $this->getSubmitButtonClass()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivacySettings::class,
            'attr' => ['class' => $this->getFormClass()]
        ]);
    }

    protected function getOptionsArray(): array
    {
        return [
            'Only Me' => PrivacySettings::ONLY_ME,
            'Only my friends' => PrivacySettings::ONLY_FRIENDS,
            'Everyone' => PrivacySettings::EVERYONE
        ];
    }
}
