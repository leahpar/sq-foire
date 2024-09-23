<?php

namespace App\DataFixtures;

use App\Entity\Hall;
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
            "1" => "Valentin Latini",
            "2" => "Lou Anne Vauchel",
            "3" => "juliette soudais",
            "4" => "Savine Lemesle",
            "5" => "MVD",
            "6" => "Marjolya Loze",
            "7" => "Enora Nouet",
            "8" => "Chloélia Pasquier",
            "9" => "Inès Lemaitre",
            "10" => "Jérémy Letailleur",
            "11" => "Maeva Corderon",
            "12" => "Pascal Percepied",
            "13" => "Eloise Tanezie",
            "14" => "Marion Lacage",
            "15" => "Oliver Guérineau",
            "16" => "Aurélia Pichou",
            "17" => "Lucie Philippe",
            "18" => "dimitri blondel",
            "19" => "Jean Luc Pierlot",
        ];

        foreach ($participants as $num => $chanteur) {
            $hall = new Hall();
            $hall->setName($chanteur);
            $hall->setTri($num);
            $manager->persist($hall);

            $question = new Question();
            $question->setQuestion("Notez la prestation de {$chanteur}");
            $question->setAnswers("0");
            $question->setHall($hall);
            $manager->persist($question);
        }

        $manager->flush();
    }

}

