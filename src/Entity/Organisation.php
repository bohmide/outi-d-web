<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'organisation est obligatoire.")] // Champ obligatoire
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom de l'organisation doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'organisation ne peut pas dépasser {{ limit }} caractères."
    )] // Longueur minimale et maximale
    private ?string $nomOrganisation = null;

   
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le nom de l'organisation est obligatoire.")] // Champ obligatoire
    #[Assert\Length(
        max: 255,
        maxMessage: "Le domaine ne peut pas dépasser {{ limit }} caractères."
    )] // Longueur maximale
    private ?string $domaine = null;

    

    /**
     * @var Collection<int, Competition>
     */
    #[ORM\OneToMany(targetEntity: Competition::class, mappedBy: 'organisation', orphanRemoval: true)]
    private Collection $competition;

    public function __construct()
    {
        $this->competition = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomOrganisation(): ?string
    {
        return $this->nomOrganisation;
    }

    public function setNomOrganisation(string $nomOrganisation): static
    {
        $this->nomOrganisation = $nomOrganisation;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(?string $domaine): static
    {
        $this->domaine = $domaine;

        return $this;
    }

    

    /**
     * @return Collection<int, Competition>
     */
    public function getCompetition(): Collection
    {
        return $this->competition;
    }

    public function addCompetition(Competition $competition): static
    {
        if (!$this->competition->contains($competition)) {
            $this->competition->add($competition);
            $competition->setOrganisation($this);
        }

        return $this;
    }

    public function removeCompetition(Competition $competition): static
    {
        if ($this->competition->removeElement($competition)) {
            // set the owning side to null (unless already changed)
            if ($competition->getOrganisation() === $this) {
                $competition->setOrganisation(null);
            }
        }

        return $this;
    }
}
