<?php

namespace App\Entity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\CompetitionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la compétition est obligatoire.")]
    #[Assert\Length(min: 3, minMessage: "Le nom de la compétition doit contenir au moins {{ limit }} caractères.")]
    private ?string $nom_comp = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'entreprise est obligatoire.")]
    private ?string $nom_entreprise = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
   
    
    private ?\DateTime $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
   
    
    #[Assert\GreaterThan(propertyPath: "date_debut", message: "La date de fin doit être postérieure à la date de début.")]
    private ?\DateTime $date_fin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 5, minMessage: "La description doit contenir au moins {{ limit }} caractères.")]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: "quiz_media", fileNameProperty: "fichier")]
    #[Assert\NotBlank(message: "Le fichier est obligatoire.")]
    #[Assert\File(
        
    maxSize: "5M",
    mimeTypes: ["image/jpeg", "image/png", "application/pdf", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"],
    mimeTypesMessage: "Seuls les fichiers de type JPG, PNG, PDF, Excel sont autorisés."
    )]
    private ?File $fichierFile = null;

    #[ORM\Column(length: 255)]
    private ?string $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'competition')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "L'organisation est obligatoire.")]
    private ?Organisation $organisation = null;

    
    


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComp(): ?string
    {
        return $this->nom_comp;
    }

    public function setNomComp(string $nom_comp): static
    {
        $this->nom_comp = $nom_comp;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nom_entreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): static
    {
        $this->nom_entreprise = $nomEntreprise;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTime $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTime $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }
    public function getFichierFile(): ?File
    {
        return $this->fichierFile;
    }

    public function setFichierFile(?File $fichierFile): void
    {
        $this->fichierFile = $fichierFile;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): static
    {
        $this->organisation = $organisation;

        return $this;
    }

    
}
