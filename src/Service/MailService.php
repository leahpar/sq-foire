<?php

namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($target, $message) {

        /*
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "jeu@smartquiz.fr",
                        'Name' => "Foire de Rouen"
                        //'Name' => "Grand Prix de la Chanson de Lillebonne"
                    ],
                    'To' => [
                        [
                            'Email' => $target,
                        ]
                    ],
                    'Subject' => "Inscription au grand jeu de la foire de Rouen",
                    //'Subject' => "Prix du public du Grand Prix de la Chanson",
                    'TextPart' => $message,
                    //'HTMLPart' => "<h3>Dear passenger 1, welcome to Mailjet!</h3><br />May the delivery force be with you!"
                ]
            ]
        ];
        $response = $this->mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
        */

        $email = (new Email())
            ->from(new Address("jeu@smartquiz.fr", "Foire de Rouen"))
            ->to($target)
            ->subject("Inscription au grand jeu de la foire de Rouen")
            ->text($message)
        ;

        $this->mailer->send($email);
    }
}
