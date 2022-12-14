<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\PrivacySettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivacySettingsFormType extends AbstractType
{
    use FormStyle;

    const ONLY_ME = 0;
    const ONLY_FRIENDS = 1;
    const EVERYONE = 2;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('friendList', ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]])
            ->add('groupList', ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]])
            ->add('albums', ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]])
            ->add('posts', ChoiceType::class, [
                'choices' => $this->getOptionsArray(),
                'required' => true,
                'attr' => ['class' => $this->getTextInputClass()],
                'label_attr' => ['class' => $this->getLabelClass()]]);
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
            'Only Me' => self::ONLY_ME,
            'Only my friends' => self::ONLY_FRIENDS,
            'Everyone' => self::EVERYONE
        ];
    }
}
