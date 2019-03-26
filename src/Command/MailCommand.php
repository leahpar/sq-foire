<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MailCommand extends Command
{
    protected static $defaultName = 'app:mail';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('mail', InputArgument::REQUIRED, 'Mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $target = $input->getArgument('mail');


        $mj = new \Mailjet\Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);

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
                            //'Name' => "passenger 1"
                        ]
                    ],
                    'Subject' => "Some test mail (" . rand(100,999) . ")",
                    'TextPart' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt...\nhttps://google.com\n",
                    //'HTMLPart' => "<h3>Dear passenger 1, welcome to Mailjet!</h3><br />May the delivery force be with you!"
                ]
            ]
        ];
        $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
        dump([
            "success" => $response->success(),
            "response" => $response->getData()
        ]);

        $io->success('Done');
    }
}
