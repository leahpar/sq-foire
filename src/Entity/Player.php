<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: \App\Repository\PlayerRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
#[UniqueEntity(fields: ['telephone'], message: 'Ce numéro de téléphone est déjà utilisé')]
class Player implements UserInterface, EquatableInterface, PasswordAuthenticatedUserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private \DateTime $firstConnection;

    #[ORM\Column]
    private \DateTime $lastConnection;

    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'player', orphanRemoval: true)]
    private $answers;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $lastRandom = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $data = [];

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column]
    private bool $fbshare = false;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column]
    private bool $authSmsPub = false;

    #[ORM\Column]
    private bool $authMailPub = false;

    public function __construct()
    {
        $this->lastConnection = new \DateTime();
        $this->firstConnection = new \DateTime();
        $this->answers = new ArrayCollection();
        $this->password = bin2hex(random_bytes(32));
    }

    public static function createAnonymeFromToken(string $token): Player
    {
        $p = new Player();
        $p->setToken($token);
        $p->setEmail("$token@smartquiz.fr");
        $p->setNom('');
        $p->setPrenom('');
        return $p;
    }

    public function isAnonyme(): bool
    {
        return empty($this->nom) && empty($this->prenom);
    }

    public function getId(): ?int
    {
        return $this->id;
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
        return $this->getNomComplet() . " (" . ($this->id ?? 0) . ")";
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

    /** @deprecated */
    public function getData()
    {
        return [
            'authMailPub' => $this->authMailPub,
            'authSmsPub' => $this->authSmsPub,
        ];
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function getRoles(): array
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
        return $this->password;
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
    public function isEqualTo(UserInterface $user): bool
    {
        return $this->getUsername() == $user->getUsername();
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNomComplet(): ?string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function isAuthSmsPub(): ?bool
    {
        return $this->authSmsPub;
    }

    public function setAuthSmsPub(bool $authSmsPub): static
    {
        $this->authSmsPub = $authSmsPub;

        return $this;
    }

    public function isAuthMailPub(): ?bool
    {
        return $this->authMailPub;
    }

    public function setAuthMailPub(bool $authMailPub): static
    {
        $this->authMailPub = $authMailPub;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

}
