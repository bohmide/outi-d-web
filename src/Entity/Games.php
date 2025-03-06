<?php

namespace App\Entity;

use App\Repository\GamesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamesRepository::class)]
class Games
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Puzzle::class, mappedBy: 'game')]
    private Collection $puzzles;

    #[ORM\OneToMany(targetEntity: MemoryCard::class, mappedBy: 'game')]
    private Collection $cards;

    public function __construct()
    {
        $this->puzzles = new ArrayCollection();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function addPuzzle(Puzzle $puzzle): static
    {
        if (!$this->puzzles->contains($puzzle)) {
            $this->puzzles->add($puzzle);
            $puzzle->setGame($this);
        }

        return $this;
    }

    public function removePuzzle(Puzzle $puzzle): static
    {
        if ($this->puzzles->removeElement($puzzle)) {
            // set the owning side to null (unless already changed)
            if ($puzzle->getGame() === $this) {
                $puzzle->setGame(null);
            }
        }

        return $this;
    }


    public function addCard(MemoryCard $puzzle): static
    {
        if (!$this->cards->contains($puzzle)) {
            $this->cards->add($puzzle);
            $puzzle->setGame($this);
        }

        return $this;
    }

    public function removeCard(MemoryCard $puzzle): static
    {
        if ($this->cards->removeElement($puzzle)) {
            // set the owning side to null (unless already changed)
            if ($puzzle->getGame() === $this) {
                $puzzle->setGame(null);
            }
        }

        return $this;
    }

    

    
}
