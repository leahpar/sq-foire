<?php

namespace App\DataFixtures;

use App\Entity\Player;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PlayersFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['players'];
    }

    public function load(ObjectManager $manager): void
    {
        $player = new Player();
        $player->setNom("Doe");
        $player->setPrenom("John");
        $player->setTelephone("+33612345678");
        $player->setEmail("john@doe.com");
        $manager->persist($player);

        $player = new Player();
        $player->setNom("Doe");
        $player->setPrenom("Jane");
        $player->setTelephone("+33612345678");
        $player->setEmail("jane@doe.com");
        $manager->persist($player);

        $player = new Player();
        $player->setNom("Johnasson");
        $player->setPrenom("Scarlett");
        $player->setTelephone("+33612345678");
        $player->setEmail("scarlett@johansson.com");
        $manager->persist($player);

        $player = new Player();
        $player->setNom("Pitt");
        $player->setPrenom("Brad");
        $player->setTelephone("+33612345678");
        $player->setEmail("brad@pitt.com");
        $manager->persist($player);

        $player = new Player();
        $player->setNom("Jolie");
        $player->setPrenom("Angelina");
        $player->setTelephone("+33612345678");
        $player->setEmail("angelina@jolie.com");
        $manager->persist($player);

        $player = new Player();
        $player->setNom("Depp");
        $player->setPrenom("Johnny");
        $player->setTelephone("+33612345678");
        $player->setEmail("johnny@depp.com");
        $manager->persist($player);

        $player = new Player();
        $player->setNom("Cruise");
        $player->setPrenom("Tom");
        $player->setTelephone("+33612345678");
        $player->setEmail("tom@cruise.com");
        $manager->persist($player);

        $manager->flush();
    }

}
