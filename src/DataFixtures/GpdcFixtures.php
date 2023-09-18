<?php

namespace App\DataFixtures;

use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class GpdcFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['gpdc'];
    }

    public function load(ObjectManager $manager): void
    {
        $participants = [
            "1"  => "Vincent Lesueur",
            "2"  => "Rémi Guionnet",
            "3"  => "Marie Holleville",
            "4"  => "Marlène Dumanoir",
            "5"  => "Maeva Corderon",
            "6"  => "Julien Dalifard",
            "7"  => "François Clatot",
            "8"  => "Flavie Affagard",
            "9"  => "Lou Anne Vauchel",
            "10"  => "Sheryne Lyamine",
            "11"  => "Céline Bardel / Sabrina Lebouvier",
            "12"  => "Jean Luc Pierlot",
            "13"  => "usanne Brebion",
            "14"  => "Jérémy Letailleur",
            "15"  => "Maxence Goffard",
        ];

        foreach ($participants as $num => $chanteur) {
            $hall = new Hall();
            $hall->setName($chanteur);
            $hall->setTri($num);
            $manager->persist($hall);

            $question = new Question();
            $question->setQuestion("Notez la prestation de {$chanteur} ?");
            $question->setAnswers("0");
            $question->setHall($hall);
            $manager->persist($question);
        }

        $manager->flush();
    }

}
