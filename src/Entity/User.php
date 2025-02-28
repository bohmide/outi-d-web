<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType("JOINED")] // Définir le type d'héritage
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "admin" => Admin::class,
    "prof" => Prof::class,
    "student" => Student::class,
    "parent" => Parents::class
])]
 class User implements UserInterface, PasswordAuthenticatedUserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[Assert\NotBlank(message:"Email adress is required")]
    #[Assert\Email(message:"Please provide a valid email address.")]
    #[ORM\Column(length: 255)]
    protected ?string $email = null;

    #[Assert\NotBlank(message:"First name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your first name must be at least {{ limit }} characters long.",
        maxMessage: "Your first name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    protected ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Last name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your last name must be at least {{ limit }} characters long.",
        maxMessage: "Your last name cannot be longer than {{ limit }} characters."
    )]    
    protected ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Password is required")]
    #[Assert\Length(
        min: 10,
        max: 20,
        minMessage: "Your password must be at least {{ limit }} characters long.",
        maxMessage: "Your password cannot be longer than {{ limit }} characters."
    )]
    protected ?string $password = null;
    private $password_verif;

    
    #[ORM\Column(type: 'json')]
    protected array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordVerif(): ?string
    {
        return $this->password_verif;
    }

    public function setPasswordVerif(string $password_verif): self
    {
        $this->password_verif = $password_verif;

        return $this;
    }
   

    public function getRoles(): array
{
    $roles = $this->roles;

    
    if (empty($roles)) {
        $roles[] = 'ROLE_USER';
    }

    return array_unique($roles);
}

public function setRoles(array $roles): self
{
    $this->roles = $roles;
    return $this;
}

public function eraseCredentials()
    {
        // If you have any sensitive data that should be erased, do it here
        $this->password = null;
    }

    /**
     * Get the user identifier (email in this case)
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email; // Assuming 'email' is your user identifier
    }

}
