<?php

namespace App\Command;

use App\Service\SMSService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SmsCommand extends Command
{
    protected static $defaultName = 'app:sms';
    /**
     * @var SMSService
     */
    private $sms;

    /**
     * SmsCommand constructor.
     * @param SMSService $sms
     */
    public function __construct(SMSService $sms)
    {
        $this->sms = $sms;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('target', InputArgument::REQUIRED, 'Target')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $target = $input->getArgument('target');
        $message = $input->getArgument('message');

        $message = "Bravo Sherlock ! Vous avez été tiré au sort, venez récupérer votre lot au stand animation GSA à l'entrée de la foire. Un soucis ? 0607840001";
        $this->sms->send($target, $message);


        $io->success('Done');
    }
}
