<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\NamedAddress;

class Mailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeMessage(User $user) {
        $email = (new Email())
            ->from('cedricplaire30@gmail.com')
            ->to('againmusician@gmail.com')
            ->subject('Bienvenue sur SoMusicShare!')
            ->text('Votre inscription sur SoMusicShare a bien été prise en compte !')
            ->html('<p>Votre inscription sur SoMusicShare a bien été prise en compte !</p>');
            /*->context([
                // You can pass whatever data you want
                'user' => $user,
            ]);*/
        $this->mailer->send($email);
    }
}