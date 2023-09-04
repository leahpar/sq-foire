<?php

namespace App\DataFixtures;

use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
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

        for ($i=1; $i<10; $i++) {
            $hall = new Hall();
            $hall->setName($i);
            $manager->persist($hall);

            $question = new Question();
            $question->setQuestion("Quelle est la bonne réponse à la question $i ?");
            $question->setAnswers(implode("\n", [
                "Réponse 1",
                "Réponse 2",
                "Réponse 3",
                "Réponse 4",
            ]));
            $question->setHall($hall);
            $manager->persist($question);
        }

        $manager->flush();
    }
}
