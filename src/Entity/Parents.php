<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ParentsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParentsRepository::class)]
class Parents extends User
{
   

    #[Assert\NotBlank(message:"First name is required")]
    #[ORM\Column(length: 255)]
    private ?string $firstName_child = null;

    #[Assert\NotBlank(message:"Last name is required")]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: "Your child's last name must be at least {{ limit }} characters long.",
        maxMessage: "Your child's last name cannot be longer than {{ limit }} characters."
    )]
    #[ORM\Column(length: 255)]
    private ?string $lastName_child = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthday_child = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe_child = null;

    #[ORM\Column(length: 255)]
    private ?string $learningDifficulties_Child = null;

    public function getRoles(): array
    {
        return ['ROLE_PARENT'];
    }

    

    public function getF_child(): ?string
    {
        return $this->firstName_child;
    }

    public function setF_child(string $firstName_child): static
    {
        $this->firstName_child = $firstName_child;

        return $this;
    }

    public function getL_child(): ?string
    {
        return $this->lastName_child;
    }

    public function setL_child(string $lnch): static
    {
        $this->lastName_child = $lnch;

        return $this;
    }

    public function getBirthday_child(): ?\DateTimeInterface
    {
        return $this->birthday_child;
    }

    public function setBirthday_child(\DateTimeInterface $dbch): static
    {
        $this->birthday_child = $dbch;

        return $this;
    }

    public function getSexe_child(): ?string
    {
        return $this->sexe_child;
    }

    public function setSexe_child(string $sch): static
    {
        $this->sexe_child = $sch;

        return $this;
    }

    public function getLearningDifficulties_Child(): ?string
    {
        return $this->learningDifficulties_Child;
    }

    public function setLearningDifficulties_Child(string $ldch): static
    {
        $this->learningDifficulties_Child = $ldch;

        return $this;
    }
    

 

    


}
