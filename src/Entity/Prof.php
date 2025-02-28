<?php

namespace App\Entity;


use App\Repository\ProfRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfRepository::class)]
class Prof extends User
{
    

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $interdate = null;

    #[ORM\Column(length: 255)]
    private ?string $intermode = null;

    
    

    

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

    public function getRoles(): array
    {
        return ['ROLE_PROF'];
    }
   
}
