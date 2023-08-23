<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HallRepository")
 */
class Hall
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tri;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="hall", cascade={"persist", "remove"})
     */
    private $questions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Pub", mappedBy="halls")
     */
    private $pubs;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->pubs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFullName(): ?string
    {
        return "Hall " . $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * @return ?Question
     */
    public function getQuestion(): ?Question
    {
        return $this->questions[0] ?? null;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setHall($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getHall() === $this) {
                $question->setHall(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Pub[]
     */
    public function getPubs(): Collection
    {
        return $this->pubs;
    }

    public function addPub(Pub $pub): self
    {
        if (!$this->pubs->contains($pub)) {
            $this->pubs[] = $pub;
            $pub->addHall($this);
        }

        return $this;
    }

    public function removePub(Pub $pub): self
    {
        if ($this->pubs->contains($pub)) {
            $this->pubs->removeElement($pub);
            $pub->removeHall($this);
        }

        return $this;
    }

    public function hasExclu(): bool
    {
        /** @var Pub $pub */
        foreach ($this->pubs as $pub) {
            if ($pub->isExclu()) return true;
        }
        return false;
    }

    public function getExclu()
    {
        /** @var Pub $pub */
        foreach ($this->pubs as $pub) {
            if ($pub->isExclu()) return $pub;
        }
        return null;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getTri(): ?int
    {
        return $this->tri;
    }

    public function setTri($tri): self
    {
        $this->tri = $tri;

        return $this;
    }
}
