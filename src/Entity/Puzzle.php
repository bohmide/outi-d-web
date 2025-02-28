<?php


namespace App\Entity;
use App\Repository\GamesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuzzleRepository::class)]
class Puzzle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'puzzles')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Games $game = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $finalImage = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $pieces = [];

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Games
    {
        return $this->game;
    }

    public function setGame(?Games $game): self
    {
        $this->game = $game;
        return $this;
    }

    public function getFinalImage(): ?string
    {
        return $this->finalImage;
    }

    public function setFinalImage(string $finalImage): self
    {
        $this->finalImage = $finalImage;
        return $this;
    }

    public function getPieces(): array
    {
        return $this->pieces;
    }

    public function setPieces(array $pieces): self
    {
        $this->pieces = $pieces;
        return $this;
    }

    
    
}
