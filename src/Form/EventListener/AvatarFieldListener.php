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
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        $perso = $user->getAvatarPerso();
        $url = $user->getGravatarUrl();

        // checks whether the user from the initial data has chosen to
        // use gravatar or not.
        if ($user->getUseGravatar()) {
            $form->add('gravatarUrl', UrlType::class, [
                'data_class' => null,
                //'mapped' => false,
                'required' => false,
                'image_property' => 'webPath'
            ]);
            $form->remove('avatarPerso');
        }
    }

    public function onPostSubmit(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if ($form['useGravatar'] == false) {
            $form->add('avatarPerso', FileType::class, [
                //'label' => 'label.youravatar',
                'data_class' => null,
                'required' => false,
                //'mapped' => false,
                'attr' => ['id' => 'avatar-perso'],
                'image_property' => 'webPath',
            ]);
            $form->remove('GravatarUrl');
        }
    }
}
