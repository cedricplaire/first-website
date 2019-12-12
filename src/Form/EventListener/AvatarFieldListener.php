<?php

namespace App\Form\EventListener;

use App\Entity\UserAvatar;
//use Symfony\Component\Form\Extension\Core\Type\EntityType;
use App\Form\UserAvatarType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AvatarFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        // checks whether the user from the initial data has chosen to
        // use gravatar or not.
        if (false === $user->getUseGravatar()) {
            $form->add('avatarPerso', FileType::class, [
                'data_class' => null,
                'label' => 'Selectionnez un fichier',
                'help' => 'le fichier peut se trouver sur votre périphérique',
                'image_property' => 'webPath'
            ]);   
        }
        else 
        {
            $form->add('avatarPerso', UrlType::class, [
                'data_class' => null,
                'label' => 'Votre Gravatar ',
                'help' => 'pour changer, selectionnez ne pas utiliser Gravatar',
                'value' => '',
                'image_property' => 'webPath'
            ]);
        }
    }

}