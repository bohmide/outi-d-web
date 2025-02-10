<?php

namespace App\Entity;

use App\Repository\EventGenreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventGenreRepository::class)]
class EventGenre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_genre = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_path = null;

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
}
