<?php

namespace App\Entity;

use App\Repository\ChapitreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;



#[ORM\Entity(repositoryClass: ChapitreRepository::class)]
class Chapitre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom du chapitre est obligatoire.")]
    #[Assert\Length(
        min: 5,
        max: 150,
        minMessage: "Le nom du chapitre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom du chapitre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom_chapitre = null;

    #[ORM\Column(type:'string',length: 255,nullable:true)]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "Le contenu du chapitre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le contenu du chapitre ne peut pas dépasser {{ limit }} caractères."
    )]    
    private ?string $contenu = null; // Stocke le chemin du fichier en base de données

    #[Assert\File(
        maxSize: '5M',
        mimeTypes: [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ],
        mimeTypesMessage: 'Veuillez uploader un fichier PDF, DOC, DOCX, PPT ou PPTX valide.'
    )]
    private ?UploadedFile $file = null; // Propriété non mappée pour l'upload

    #[ORM\ManyToOne(inversedBy: 'chapitres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\OneToOne(inversedBy: 'chapitre', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete:"SET NULL")]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        min: 10,
        minMessage: "Le contenu du chapitre doit contenir au moins {{ limit }} caractères."
    )]
     private ?string $contenuText = null;

   public function getContenuText(): ?string
  {
    return $this->contenuText;
  }

   public function setContenuText(?string $contenuText): static
   {
    $this->contenuText = $contenuText;
    return $this;
  }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomChapitre(): ?string
    {
        return $this->nom_chapitre;
    }

    public function setNomChapitre(string $nom_chapitre): static
    {
        $this->nom_chapitre = $nom_chapitre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        $this->cours = $cours;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }
    public function getFile(): ?UploadedFile
{
    return $this->file;
}

public function setFile(?UploadedFile $file): self
{
    $this->file = $file;
    return $this;
}
}
