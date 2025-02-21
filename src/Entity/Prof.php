<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProfRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfRepository::class)]
class Prof
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
    private ?string $fnpr = null;

    #[Assert\NotBlank(message:"Last name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your last name must be at least {{ limit }} characters long.",
        maxMessage: "Your last name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $lnpr = null;

    #[Assert\NotBlank(message:"Password is required")]
    #[Assert\Length(
        min: 10,
        max: 20,
        minMessage: "Your password must be at least {{ limit }} characters long.",
        maxMessage: "Your password cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $pwpr = null;

    #[Assert\NotBlank(message: "Password confirmation is required")]
    #[Assert\EqualTo(
        propertyPath: "pwpr",
        message: "The password confirmation does not match the password."
    )]
    #[ORM\Column(length: 255)]
    private ?string $pvpr = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $interdate = null;

    #[ORM\Column(length: 255)]
    private ?string $intermode = null;

    #[Assert\NotBlank(message:"Email adress is required")]
    #[Assert\Email(message:"Please provide a valid email address.")]
    #[ORM\Column(length: 255)]
    private ?string $emailpr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFnpr(): ?string
    {
        return $this->fnpr;
    }

    public function setFnpr(string $fnpr): static
    {
        $this->fnpr = $fnpr;

        return $this;
    }

    public function getLnpr(): ?string
    {
        return $this->lnpr;
    }

    public function setLnpr(string $lnpr): static
    {
        $this->lnpr = $lnpr;

        return $this;
    }

    public function getPwpr(): ?string
    {
        return $this->pwpr;
    }

    public function setPwpr(string $pwpr): static
    {
        $this->pwpr = $pwpr;

        return $this;
    }

    public function getPvpr(): ?string
    {
        return $this->pvpr;
    }

    public function setPvpr(string $pvpr): static
    {
        $this->pvpr = $pvpr;

        return $this;
    }

    public function getInterdate(): ?\DateTimeInterface
    {
        return $this->interdate;
    }

    public function setInterdate(\DateTimeInterface $interdate): static
    {
        $this->interdate = $interdate;

        return $this;
    }

    public function getIntermode(): ?string
    {
        return $this->intermode;
    }

    public function setIntermode(string $intermode): static
    {
        $this->intermode = $intermode;

        return $this;
    }

    public function getEmailpr(): ?string
    {
        return $this->emailpr;
    }

    public function setEmailpr(string $emailpr): static
    {
        $this->emailpr = $emailpr;

        return $this;
    }
}
