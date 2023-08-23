<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hall", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hall;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $question;

    /**
     * @ORM\Column(type="text")
     */
    private $answers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="question", orphanRemoval=true)
     */
    private $toto; // Useless, just for the orphanRemoval.

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHall(): ?Hall
    {
        return $this->hall;
    }

    public function setHall(?Hall $hall): self
    {
        $this->hall = $hall;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswers(): ?string
    {
        return $this->answers;
    }

    public function getGoodAnswer(): ?string
    {
        return $this->getAnswer(0);
    }

    public function isGoodAnswer(string $answer): bool
    {
        return $answer == $this->getGoodAnswer();
    }

    public function getAnswer(int $pos): ?string
    {
        $answers = explode("\n", (string) $this->answers);
        return $answers[$pos] ?? null;
    }

    public function getAnswersCount(): int
    {
        $answers = explode("\n", (string) $this->answers);
        return count($answers);
    }

    /**
     * (for Admin)
     * @return string
     */
    public function getAnswersHtml(): string
    {
        $str  = "<span style='color:green;'>";
        $str .= str_replace("\n", "</span>; <span>", strip_tags((string) $this->answers));
        $str .= "</span>";

        return $str;
    }

    /**
     * (for players)
     * @return array
     */
    public function getAnswersRandom(): array
    {
        $answers = explode("\n", (string) $this->answers);
        shuffle($answers);
        return $answers;
    }

    public function setAnswers(string $answers): self
    {
        $this->answers = str_replace("\r", '', $answers);

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function __toString()
    {
        return $this->question;
    }
}
