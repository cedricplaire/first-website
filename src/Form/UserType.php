<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use App\Form\EventListener\AvatarFieldListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * Defines the form used to edit an user with fileupload or service for avatar.
 *
 * @author Plaire Cédric <againmusician@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'label.username',
                'disabled' => true,
            ])
            ->add('fullName', TextType::class, [
                'label' => 'label.fullname',
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'label.firstname'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname'
            ])
            ->add('birthday', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'label.birthday'
            ])
            ->add('age', NumberType::class, [
                'label' => 'label.age'
            ])
            ->add('address', PostalAdressType::class)
            ->add('useGravatar', CheckboxType::class, [
                'label' => 'label.usegravatar',
                'required' => false,
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->add('avatarPerso', FileType::class, [
                'label' => 'label.youravatar',
                'data_class' => null,
                'required' => false,
                //'mapped' => false,
                'attr' => ['id' => 'avatar-perso'],
                'image_property' => 'webPath',
            ])
            /*->add('gravatarUrl', UrlType::class, [
                'data_class' => null,
                //'mapped' => false,
                'required' => false,
                'image_property' => 'webPath'
            ]);*/
            ->addEventSubscriber(new AvatarFieldListener());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
