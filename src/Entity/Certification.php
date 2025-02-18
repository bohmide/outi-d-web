<?php

namespace App\Entity;

use App\Repository\CertificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRepository::class)]
class Certification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_certification = null;

    #[ORM\OneToOne(mappedBy: 'certification')]
    private ?Cours $cours = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCertification(): ?string
    {
        return $this->nom_certification;
    }

    public function setNomCertification(string $nom_certification): static
    {
        $this->nom_certification = $nom_certification;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        // unset the owning side of the relation if necessary
        if ($cours === null && $this->cours !== null) {
            $this->cours->setCertification(null);
        }

        // set the owning side of the relation if necessary
        if ($cours !== null && $cours->getCertification() !== $this) {
            $cours->setCertification($this);
        }

        $this->cours = $cours;

        return $this;
    }
}
