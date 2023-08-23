<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: \App\Repository\PlayerRepository::class)]
class Player implements UserInterface, EquatableInterface, PasswordAuthenticatedUserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $firstConnection;

    #[ORM\Column(type: 'datetime')]
    private $lastConnection;

    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'player', orphanRemoval: true)]
    private $answers;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastRandom;

    #[ORM\Column(type: 'json', nullable: true)]
    private $data;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'boolean')]
    private $fbshare = false;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $token;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        $this->lastConnection = new \DateTime();
        $this->firstConnection = new \DateTime();
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->lastConnection;
    }

    public function setLastConnection(\DateTimeInterface $lastConnection): self
    {
        $this->lastConnection = $lastConnection;

        return $this;
    }

    public function getFirstConnection(): ?\DateTimeInterface
    {
        return $this->firstConnection;
    }

    public function setFirstConnection(\DateTimeInterface $firstConnection): self
    {
        $this->firstConnection = $firstConnection;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setPlayer($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getPlayer() === $this) {
                $answer->setPlayer(null);
            }
        }

        return $this;
    }

    public function countAnswers(): int
    {
        return count($this->answers);
    }

    public function countGoodAnswers(): int
    {
        $c = 0;
        /** @var Answer $a */
        foreach ($this->answers as $a) {
            $c += $a->isGood() ? 1 : 0;
        }
        return $c;
    }

    public function __toString()
    {
        return $this->getName() . " (" . ($this->id ?? 0) . ")";
    }

    public function updateLastConnection($date = null)
    {
        $this->lastConnection = $date ?? (new \DateTime());
    }

    public function getLastRandom(): ?\DateTimeInterface
    {
        return $this->lastRandom;
    }

    public function setLastRandom(?\DateTimeInterface $lastRandom): self
    {
        $this->lastRandom = $lastRandom;

        return $this;
    }

    public function isRandom(): bool
    {
        return $this->lastRandom !== null;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function getRoles()
    {
        return ["ROLE_USER"];
    }

    public function isHallDone(int $id): bool
    {
        foreach ($this->getAnswers() as $a) {
            if ($id == $a->getQuestion()->getHall()->getId()) return true;
        }
        return false;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword(): ?string
    {
        return '';
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }


    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        return $this->getUsername() == $user->getUsername();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFbshare(): ?bool
    {
        return $this->fbshare;
    }

    public function setFbshare(bool $fbshare): self
    {
        $this->fbshare = $fbshare;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

}
