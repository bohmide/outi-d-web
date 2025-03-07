<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom du cours ne peut pas être vide")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le nom du cours doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom du cours ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "L'état du cours ne peut pas être vide")]
    #[Assert\Choice(
        choices: ["Facile", "Moyen", "Avancé"],
        message: "L'état du cours doit être 'Facile', 'Moyen' ou 'Avancé'"
    )]
    private ?string $etat = "Facile";  // Valeur par défaut

    /**
     * @var Collection<int, Chapitre>
     */
    #[ORM\OneToMany(targetEntity: Chapitre::class, mappedBy: 'cours',cascade: ['persist', 'remove'])]
    private Collection $chapitres;

    #[ORM\OneToOne(inversedBy: 'cours', cascade: ['persist', 'remove'])]
    private ?Certification $certification = null;
    public function isCertifie(): bool
     {
    return $this->certification !== null;
     }

    public function __construct()
    {
        $this->chapitres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Chapitre>
     */
    public function getChapitres(): Collection
    {
        return $this->chapitres;
    }

    public function addChapitre(Chapitre $chapitre): static
    {
        if (!$this->chapitres->contains($chapitre)) {
            $this->chapitres->add($chapitre);
            $chapitre->setCours($this);
        }

        return $this;
    }

    public function removeChapitre(Chapitre $chapitre): static
    {
        if ($this->chapitres->removeElement($chapitre)) {
            // set the owning side to null (unless already changed)
            if ($chapitre->getCours() === $this) {
                $chapitre->setCours(null);
            }
        }

        return $this;
    }

    public function getCertification(): ?Certification
    {
        return $this->certification;
    }

    public function setCertification(?Certification $certification): static
    {
        if ($this->isCertifie() && !$certification) {
            throw new \LogicException('Un cours certifié doit avoir une certification');
        }
        $this->certification = $certification;
        if ($certification) {
            $certification->setCours($this);
        }
        return $this;
    }
    public function getCertificationPath(): string
{
    return '/public/uploads/certifications/' . $this->id . '.pdf';
}

}
