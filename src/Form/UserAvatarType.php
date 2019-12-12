<?php

namespace App\Form;

use App\Entity\UserAvatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserAvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Avatar :',
                'placeholder' => 'Choisissez une image sur votre périphérique',
                'help' => 'La taille maximale de l\'image est 512 x 512',
                'image_property' => 'webPath'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAvatar::class,
        ]);
    }
}
