<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $answer = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    #[ORM\Column(type: 'boolean')]
    private bool $good;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Player::class, inversedBy: 'answers')]
        #[ORM\JoinColumn(nullable: false)]
        private Player $player,
        /**
         * @var Question
         */
        #[ORM\ManyToOne(targetEntity: Question::class)]
        #[ORM\JoinColumn(nullable: false)]
        private Question $question,
    ) {
        $this->date = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;
        $this->good = $answer == $this->question->getGoodAnswer();

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function isGood(): ?bool
    {
        return $this->good;
    }

    public function setGood(bool $good): self
    {
        $this->good = $good;

        return $this;
    }

    public function __toString()
    {
        return
            "(Hall " . $this->question->getHall()->getName() . ")"
            . " "
            . $this->question->getQuestion()
            . " => "
            . $this->answer
            . " "
            . ($this->isGood() ? "[OK]" : "[KO]")
        ;
    }
}
