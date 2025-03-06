<?php

namespace App\Entity;

use App\Repository\EventGenreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventGenreRepository::class)]
class EventGenre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "nom is require")]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $nom_genre = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_path = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime(); // Set current date
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomGenre(): ?string
    {
        return $this->nom_genre;
    }

    public function setNomGenre(string $nom_genre): static
    {
        $this->nom_genre = $nom_genre;

        return $this;
    }

    public function getNbr(): ?int
    {
        return $this->nbr;
    }

    public function setNbr(?int $nbr): static
    {
        $this->nbr = $nbr;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}
