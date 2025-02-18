<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ParentsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;;

#[ORM\Entity(repositoryClass: ParentsRepository::class)]
class Parents implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message:"First name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your first name must be at least {{ limit }} characters long.",
        maxMessage: "Your first name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $fnp = null;


    #[Assert\NotBlank(message:"Last name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your last name must be at least {{ limit }} characters long.",
        maxMessage: "Your last name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $lnp = null;


    #[Assert\NotBlank(message:"Password is required")]
    #[Assert\Length(
        min: 10,
        max: 20,
        minMessage: "Your password must be at least {{ limit }} characters long.",
        maxMessage: "Your password cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $pwp = null;


    #[Assert\NotBlank(message: "Password confirmation is required")]
    #[Assert\EqualTo(
        propertyPath: "pwp",
        message: "The password confirmation does not match the password."
    )]
    #[ORM\Column(length: 255)]
    private ?string $pvp = null;

    #[Assert\NotBlank(message:"First name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your child's first name must be at least {{ limit }} characters long.",
        maxMessage: "Your child's first name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $fnch = null;

    #[Assert\NotBlank(message:"Last name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your child's last name must be at least {{ limit }} characters long.",
        maxMessage: "Your child's last name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $lnch = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dbch = null;

    #[ORM\Column(length: 255)]
    private ?string $sch = null;

    #[ORM\Column(length: 255)]
    private ?string $ldch = null;

    #[Assert\NotBlank(message:"Email adress is required")]
    #[Assert\Email(message:"Please provide a valid email address.")]
    #[ORM\Column(length: 255)]
    private ?string $emailp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFnp(): ?string
    {
        return $this->fnp;
    }

    public function setFnp(string $fnp): static
    {
        $this->fnp = $fnp;

        return $this;
    }

    public function getLnp(): ?string
    {
        return $this->lnp;
    }

    public function setLnp(string $lnp): static
    {
        $this->lnp = $lnp;

        return $this;
    }

    public function getPwp(): ?string
    {
        return $this->pwp;
    }

    public function setPwp(string $pwp): static
    {
        $this->pwp = $pwp;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPvp(): ?string
    {
        return $this->pvp;
    }

    public function setPvp(string $pvp): static
    {
        $this->pvp = $pvp;

        return $this;
    }

    public function getFnch(): ?string
    {
        return $this->fnch;
    }

    public function setFnch(string $fnch): static
    {
        $this->fnch = $fnch;

        return $this;
    }

    public function getLnch(): ?string
    {
        return $this->lnch;
    }

    public function setLnch(string $lnch): static
    {
        $this->lnch = $lnch;

        return $this;
    }

    public function getDbch(): ?\DateTimeInterface
    {
        return $this->dbch;
    }

    public function setDbch(\DateTimeInterface $dbch): static
    {
        $this->dbch = $dbch;

        return $this;
    }

    public function getSch(): ?string
    {
        return $this->sch;
    }

    public function setSch(string $sch): static
    {
        $this->sch = $sch;

        return $this;
    }

    public function getLdch(): ?string
    {
        return $this->ldch;
    }

    public function setLdch(string $ldch): static
    {
        $this->ldch = $ldch;

        return $this;
    }

    public function getEmailp(): ?string
    {
        return $this->emailp;
    }

    public function setEmailp(string $emailp): static
    {
        $this->emailp = $emailp;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporairement, nettoie-les ici
    }

    public function getUserIdentifier(): string
    {
        return $this->emailp;
    }
}
