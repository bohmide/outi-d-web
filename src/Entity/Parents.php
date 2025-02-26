<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ParentsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParentsRepository::class)]
class Parents extends User
{
    #[Assert\NotBlank(message: "First name is required")]
    #[ORM\Column(length: 255)]
    private ?string $firstNameChild = null;

    #[Assert\NotBlank(message: "Last name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your child's last name must be at least {{ limit }} characters long.",
        maxMessage: "Your child's last name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $lastNameChild = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthdayChild = null;

    #[ORM\Column(length: 255)]
    private ?string $sexeChild = null;

    #[ORM\Column(length: 255)]
    private ?string $learningDifficultiesChild = null;

    public function getRoles(): array
    {
        return ['ROLE_PARENT'];
    }   

    // Corrected Getter and Setter for firstNameChild
    public function getFirstNameChild(): ?string
    {
        return $this->firstNameChild;
    }

    public function setFirstNameChild(string $firstNameChild): static
    {
        $this->firstNameChild = $firstNameChild;
        return $this;
    }

    // Corrected Getter and Setter for lastNameChild
    public function getLastNameChild(): ?string
    {
        return $this->lastNameChild;
    }

    public function setLastNameChild(string $lastNameChild): static
    {
        $this->lastNameChild = $lastNameChild;
        return $this;
    }

    public function getBirthdayChild(): ?\DateTimeInterface
    {
        return $this->birthdayChild;
    }

    public function setBirthdayChild(\DateTimeInterface $birthdayChild): static
    {
        $this->birthdayChild = $birthdayChild;
        return $this;
    }

    public function getSexeChild(): ?string
    {
        return $this->sexeChild;
    }

    public function setSexeChild(string $sexeChild): static
    {
        $this->sexeChild = $sexeChild;
        return $this;
    }

    public function getLearningDifficultiesChild(): ?string
    {
        return $this->learningDifficultiesChild;
    }

    public function setLearningDifficultiesChild(string $learningDifficultiesChild): static
    {
        $this->learningDifficultiesChild = $learningDifficultiesChild;
        return $this;
    }
}
