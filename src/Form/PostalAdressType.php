<?php

namespace App\Form;

use App\Entity\PostalAdress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PostalAdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', TextType::class, [
                'label' => 'formlabel-address-street',
                'help' => 'formhelp-address-street',
            ])
            ->add('building', TextType::class, [
                'label' => 'formlabel-address-building',
                'help' => 'formhelp-address-building',
            ])
            ->add('city', TextType::class, [
                'label' => 'formlabel-address-city',
                'help' => 'formhelp-address-city',
            ])
            ->add('zipCode', NumberType::class, [
                'label' => 'formlabel-address-zip',
                'help' => 'formhelp-address-zip',
            ])
            ->add('state', TextType::class, [
                'label' => 'formlabel-address-state',
                'help' => 'formhelp-address-state',
            ])
            ->add('country', TextType::class, [
                'label' => 'formlabel-address-country',
                'help' => 'formhelp-address-country',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostalAdress::class,
        ]);
    }
}
