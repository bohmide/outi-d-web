<?php

namespace App\Entity;

use App\Repository\EvenementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EvenementsRepository::class)]


class Evenements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "nom is require")]
    #[ORM\Column(length: 255)]
    private ?string $nom_event = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank(message: "date is require")]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_event = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbr_members = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_path = null;

    #[Assert\NotBlank(message: "genre is require")]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?EventGenre $genre = null;

    /**
     * @var Collection<int, Sponsors>
     */
    #[ORM\ManyToMany(targetEntity: Sponsors::class, inversedBy: 'evenements')]
    private Collection $sponsors;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?float $prix = null;


    public function __construct()
    {
        $this->sponsors = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEvent(): ?string
    {
        return $this->nom_event;
    }

    public function setNomEvent(string $nom_event): static
    {
        $this->nom_event = $nom_event;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateEvent(): ?\DateTimeInterface
    {
        return $this->date_event;
    }

    public function setDateEvent(\DateTimeInterface $date_event): static
    {
        $this->date_event = $date_event;

        return $this;
    }

    public function getNbrMembers(): ?int
    {
        return $this->nbr_members;
    }

    public function setNbrMembers(?int $nbr_members): static
    {
        $this->nbr_members = $nbr_members;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->image_path;
    }

    public function setImagePath(?string $image_path): static
    {
        $this->image_path = $image_path;

        return $this;
    }

    public function getGenre(): ?EventGenre
    {
        return $this->genre;
    }

    public function setGenre(?EventGenre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, Sponsors>
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    public function addSponsor(Sponsors $sponsor): static
    {
        if (!$this->sponsors->contains($sponsor)) {
            $this->sponsors->add($sponsor);
        }

        return $this;
    }

    public function removeSponsor(Sponsors $sponsor): static
    {
        $this->sponsors->removeElement($sponsor);

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

}
