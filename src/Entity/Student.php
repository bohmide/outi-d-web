<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\StudentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends User
{
    

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datebirth = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    

    
    
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

    

    public function getRoles(): array
    {
        return ['ROLE_STUDENT'];
    }
}
