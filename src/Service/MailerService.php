<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEquipeCreatedEmail(
        string $nomComp, 
        string $nomEquipe, 
        string $nomAmbassadeur, 
        array $membresEmails
    ) {
        $email = (new Email())
            ->from(new Address('your_email@gmail.com','Outi-d competition STAFF'))
            ->to('salmaachour2015@gmail.com') // Destinataire statique
            ->subject('Nouvelle équipe créée')
            ->html("
                <p>Une nouvelle équipe a été créée :</p>
                <ul>
                    <li><strong>Nom de la compétition :</strong> $nomComp</li>
                    <li><strong>Nom de l'équipe :</strong> $nomEquipe</li>
                    <li><strong>Nom de l'ambassadeur :</strong> $nomAmbassadeur</li>
                    <li><strong>Membres :</strong> " . implode(', ', $membresEmails) . "</li>
                </ul>
            ");

        $this->mailer->send($email);
    }
}
