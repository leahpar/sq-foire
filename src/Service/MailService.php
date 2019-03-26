<?php

namespace App\Service;


class MailService
{
    /** @var \Mailjet\Client */
    private $mj;


    /**
     * MailService constructor.
     */
    public function __construct()
    {
        $this->mj = new \Mailjet\Client(
            getenv('MJ_APIKEY_PUBLIC'),
            getenv('MJ_APIKEY_PRIVATE'),
            true,
            ['version' => 'v3.1']
        );
    }

    public function send($target, $message) {

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "jeu@smartquiz.fr",
                        'Name' => "Enquête à la foire"
                    ],
                    'To' => [
                        [
                            'Email' => $target,
                        ]
                    ],
                    'Subject' => "Inscription à l'enquête à la foire",
                    'TextPart' => $message,
                    //'HTMLPart' => "<h3>Dear passenger 1, welcome to Mailjet!</h3><br />May the delivery force be with you!"
                ]
            ]
        ];
        $response = $this->mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
        return $response;
    }
}
