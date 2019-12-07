<?php

namespace App\Form;

use App\Entity\UserMessage;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('sujet', TextType::class, [
            'help' => 'Entrez le sujet du message (présentation, aide, demande ..)',
        ])
        ->add('content', TextareaType::class, [
            'help' => 'Texte de votre message (détaillez le plus possible)',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserMessage::class,
        ]);
    }
}
