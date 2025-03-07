<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MemoryCardRepository;

#[ORM\Entity(repositoryClass: MemoryCardRepository::class)]
class MemoryCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // Liste des images
    #[ORM\Column(type: 'json')]
    private array $images = [];

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Games $game = null;



    // Getter et setter


    public function getGame(): ?Games
    {
        return $this->game;
    }

    public function setGame(?Games $game): self
    {
        $this->game = $game;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImages(): array
{
    return $this->images ?? [];  // Retourne un tableau vide au lieu de null
}

public function setImages(array $images): self
{
    $this->images = $images;
    return $this;
}
}