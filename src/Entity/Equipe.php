<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $travail = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'équipe est obligatoire.")] // Champ obligatoire
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom de l'équipe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'équipe ne peut pas dépasser {{ limit }} caractères."
    )] // Longueur minimale et maximale
    private ?string $nomEquipe = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'ambassadeur est obligatoire.")] // Champ obligatoire
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom de l'ambassadeur doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'ambassadeur ne peut pas dépasser {{ limit }} caractères."
    )] // Longueur minimale et maximale
    private ?string $ambassadeur = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank(message: "Les membres de l'équipe sont obligatoires.")]
    #[Assert\Count(
        min: 3,
        minMessage: "Vous devez ajouter au moins {{ limit }} membres pour former une équipe."
    )]
    #[Assert\All([
        new Assert\Email(message: "L'adresse e-mail '{{ value }}' n'est pas valide. Veuillez saisir une adresse e-mail valide.")
    ])]
    private array $membres = [];

    /**
     * @var Collection<int, Competition>
     */
    #[ORM\ManyToMany(targetEntity: Competition::class, inversedBy: 'equipes')]
    #[ORM\JoinTable(name: 'competition_equipe')]
    private Collection $competitions;

  


    public function __construct()
    {
        $this->competitions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEquipe(): ?string
    {
        return $this->nomEquipe;
    }

    public function setNomEquipe(string $nomEquipe): static
    {
        $this->nomEquipe = $nomEquipe;

        return $this;
    }

    public function getAmbassadeur(): ?string
    {
        return $this->ambassadeur;
    }

    public function setAmbassadeur(string $ambassadeur): static
    {
        $this->ambassadeur = $ambassadeur;

        return $this;
    }

    public function getMembres(): array
    {
        return $this->membres;
    }

    public function setMembres(array $membres): static
    {
        $this->membres = $membres;

        return $this;
    }

    public function getTravail(): ?string
{
    return $this->travail;
}

public function setTravail(?string $travail): static
{
    $this->travail = $travail;
    return $this;
}



    /**
     * @return Collection<int, Competition>
     */
    public function getCompetitions(): Collection
    {
        return $this->competitions;
    }

    public function addCompetition(Competition $competition): static
{
    if (!$this->competitions->contains($competition)) {
        $this->competitions[] = $competition;
        $competition->addEquipe($this); // Synchronisation bidirectionnelle
    }
    return $this;
}


    public function removeCompetition(Competition $competition): static
    {
        if ($this->competitions->removeElement($competition)) {
            $competition->removeEquipe($this); // Synchronisation bidirectionnelle
        }
        return $this;
    }
}
