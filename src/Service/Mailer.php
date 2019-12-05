<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class Mailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeMessage(User $user) {
        $email = (new TemplatedEmail())
            ->from('cedricplaire30@gmail.com')
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Bienvenue sur SoMusicShare!')
            //->text('Votre inscription sur SoMusicShare a bien été prise en compte !')
            ->htmlTemplate('emails/registration/confirm.html.twig')
            ->context([
                // You can pass whatever data you want
                'name' => $user->getFullName(),
            ]);
        $this->mailer->send($email);
    }
}