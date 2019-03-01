<?php

namespace App\Command;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitCommand extends Command
{
    protected static $defaultName = 'app:init';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * InitCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Init database')
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        //$arg1 = $input->getArgument('arg1');

        //if ($arg1) {
        //    $io->note(sprintf('You passed an argument: %s', $arg1));
        //}

        //if ($input->getOption('option1')) {
        //    // ...
        //}

        $em = $this->em;
        $connection = $em->getConnection();


        $ret = $connection->fetchAll("SELECT * FROM sqlite_sequence WHERE name = 'player'");
        $startSeq = $ret[0]["seq"];

        if ($startSeq < 1000) {
            for ($i = 0; $i < 1324; $i++) {
                $p = new Player();
                $p  ->setEmail("toto")
                    ->setName("toto");
                $em->persist($p);
            }
            $em->flush();

            $players = $em->getRepository(Player::class)->findByName("toto");
            foreach ($players as $p) {
                $em->remove($p);
            }
            $em->flush();
        }

        $ret = $connection->fetchAll("SELECT * FROM sqlite_sequence WHERE name = 'player'");
        $seq = $ret[0]["seq"];

        $io->success("Done. Sequence $startSeq => $seq");
    }
}
