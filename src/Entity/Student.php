<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\StudentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
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
    private ?string $fn = null;
    
    #[Assert\NotBlank(message:"Last name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your last name must be at least {{ limit }} characters long.",
        maxMessage: "Your last name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $ln = null;

    #[Assert\NotBlank(message:"Password is required")]
    #[Assert\Length(
        min: 10,
        max: 20,
        minMessage: "Your password must be at least {{ limit }} characters long.",
        maxMessage: "Your password cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $pw = null;

    #[Assert\NotBlank(message: "Password confirmation is required")]
    #[Assert\EqualTo(
        propertyPath: "pwp",
        message: "The password confirmation does not match the password."
    )]
    #[ORM\Column(length: 255)]
    private ?string $pv = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datebirth = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    private ?string $ld = null;

    #[Assert\NotBlank(message:"Email adress is required")]
    #[Assert\Email(message:"Please provide a valid email address.")]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFn(): ?string
    {
        return $this->fn;
    }

    public function setFn(string $fn): static
    {
        $this->fn = $fn;

        return $this;
    }

    public function getLn(): ?string
    {
        return $this->ln;
    }

    public function setLn(string $ln): static
    {
        $this->ln = $ln;

        return $this;
    }

    public function getPw(): ?string
    {
        return $this->pw;
    }

    public function setPw(string $pw): static
    {
        $this->pw = $pw;

        return $this;
    }

    public function getPv(): ?string
    {
        return $this->pv;
    }

    public function setPv(string $pv): static
    {
        $this->pv = $pv;

        return $this;
    }

    public function getDatebirth(): ?\DateTimeInterface
    {
        return $this->datebirth;
    }

    public function setDatebirth(\DateTimeInterface $datebirth): static
    {
        $this->datebirth = $datebirth;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getLd(): ?string
    {
        return $this->ld;
    }

    public function setLd(string $ld): static
    {
        $this->ld = $ld;

        return $this;
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
}
