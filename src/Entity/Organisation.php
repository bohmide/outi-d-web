<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomOrganisation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domaine = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

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

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

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
