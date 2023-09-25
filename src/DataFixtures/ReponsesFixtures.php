<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Player;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ReponsesFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['reponses'];
    }

    public function load(ObjectManager $manager): void
    {
        $players = $manager->getRepository(Player::class)->findAll();
        $questions = $manager->getRepository(Question::class)->findAll();
            /** @var Player $player */
        for ($i=1; $i<100; $i++) {
            $player = $players[random_int(0, count($players) - 1)];
            /** @var Question $question */
            $question = $questions[random_int(0, count($questions) - 1)];
            $reponse = new Answer($player, $question);
            $reponse->setAnswer($question->getAnswer(random_int(0, $question->getAnswersCount()-1)));
            $manager->persist($reponse);
        }

        $manager->flush();
    }

}
