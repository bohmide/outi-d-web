<?php

namespace App\Entity;

use App\Repository\QuizKidsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: QuizKidsRepository::class)]
class QuizKids
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 10, minMessage: "La question doit contenir au moins 10 caractères.")]

    private ?string $question = null;

    
    #[ORM\Column(type: Types::JSON)]
    #[Assert\Count(
        min: 2,
        max: 4,
        minMessage: "Ajoutez au moins {{ limit }} options.",
        maxMessage: "Maximum {{ limit }} options autorisées."
    )]
    #[Assert\Unique(message: "Les options doivent être uniques")]
    #[Assert\All([
        new Assert\NotBlank(message: "Une option ne peut être vide"),
        new Assert\Length(
            max: 100,
            maxMessage: "Une option ne peut dépasser {{ limit }} caractères"
        )
    ])]
    private array $options = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Sélectionnez la réponse correcte.")]
    private ?string $correctAnswer = null;

    #[Vich\UploadableField(mapping: "quiz_media", fileNameProperty: "media")]
    ##[Assert\NotBlank(message: "Le media File est obligatoire.")]
    private ?File $mediaFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    
    private ?string $media = null;

    #[ORM\Column(length: 255)]
   
    #[Assert\NotBlank(message: "Sélectionnez un niveau de difficulté.")]
    private ?string $level = null;

    #[ORM\Column(length: 255)]
    
    private ?string $score = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Sélectionnez un genre.")]
    private ?string $genre = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Challenge $challenge = null;

    #[ORM\Column(length: 255, nullable: true)]
    
    private ?string $country;

    public function setMediaFile(?File $file = null): void
    {
        $this->mediaFile = $file;
        if ($file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getMediaFile(): ?File
    {
        return $this->mediaFile;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): self
    {
        $this->media = $media;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getCorrectAnswer(): ?string
    {
        return $this->correctAnswer;
    }

    public function setCorrectAnswer(string $correctAnswer): static
    {
        $this->correctAnswer = $correctAnswer;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(string $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getIdChallenge(): ?Challenge
    {
        return $this->challenge;
    }

    public function setIdChallenge(?Challenge $challenge): static
    {
        $this->challenge = $challenge;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }
}
