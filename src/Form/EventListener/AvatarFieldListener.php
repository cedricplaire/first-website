<?php

namespace App\Form\EventListener;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AvatarFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            //FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        $options = [
            'attr' => ['class' => 'd-none']
        ];
        // checks whether the user from the initial data has chosen to
        // use gravatar or not.
        if (false === $user->getUseGravatar()) {
            $form->add('avatarPerso', FileType::class, [
                'label' => 'label.youravatar',
                'data_class' => null,
                //'required' => false,
                'attr' => ['placeholder' => 'label.youravatarfile',
                    'class' => 'avatar-perso'],
                'image_property' => 'webPath'
            ]);  

        }
        else 
        { 
            $form->add('gravatarUrl', UrlType::class, [
                'label' => 'label.gravatarurl',
                'required' => false,
                'help' => 'label.generatedurl',
                'attr' => ['placeholer' => 'label.generatedurl',
                    'class' => 'url-gravatar'],
                'image_property' => 'webPath'
            ]);
        }
    }
}