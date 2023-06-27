<?php

namespace App\Entity;

use App\Entity\Common\DatedInterface;
use App\Entity\Common\DatedTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "bookcamp_users")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, DatedInterface
{
    use DatedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Unique(message: "Un utilisateur existe déjà avec cette adresse email")]
    #[Assert\NotBlank(message: "Cette valeur ne peut pas être vide")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Cette valeur ne peut pas être vide")]
    #[Assert\Regex(
        pattern: "/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%])(?!.*(.)\1{2}).*[a-z]/m",
        message: "Votre mot de passe doit comporter au moins huit caractères, dont des lettres majuscules et minuscules, un chiffre et un symbole.",
        match: true
    )]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Cette valeur ne peut pas être vide")]
    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Account $account = null;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $lastLoggedIn = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        // set the owning side of the relation if necessary
        if ($account->getUser() !== $this) {
            $account->setUser($this);
        }

        $this->account = $account;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    /**
     * @param string|null $resetToken
     */
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLoggedIn(): ?\DateTime
    {
        return $this->lastLoggedIn;
    }

    /**
     * @param \DateTime|null $lastLoggedIn
     */
    public function setLastLoggedIn(?\DateTime $lastLoggedIn): void
    {
        $this->lastLoggedIn = $lastLoggedIn;
    }

    public function isEnabled(): ?bool
    {
        if($this->getAccount()) {
            return $this->getAccount()->getStatus() === Account::ACCOUNT_STATUS_ACTIVE;
        }
        return false;
    }
}
