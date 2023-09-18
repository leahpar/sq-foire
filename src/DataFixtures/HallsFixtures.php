<?php

namespace App\DataFixtures;

use App\Entity\Hall;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class HallsFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['halls'];
    }

    public function load(ObjectManager $manager): void
    {
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
