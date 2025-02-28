<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La réponse ne peut pas être vide.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "La réponse doit comporter au moins {{ limit }} caractères.",
     maxMessage: "La réponse ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $reponse = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La valeur de la réponse doit être spécifiée.")]
    private ?bool $isCorrect = null;

    #[ORM\ManyToOne(inversedBy: 'reponse')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): static
    {
        if ($isCorrect && $this->question && $this->question->getType() === 'choix_unique') {
            $existingCorrectAnswers = $this->question->getReponse()->filter(fn(Reponse $r) => $r->isCorrect());
    
            // Vérifier si une autre réponse est déjà correcte et ce n'est pas celle-ci qu'on modifie
            if (count($existingCorrectAnswers) > 0 && !$existingCorrectAnswers->contains($this)) {
                // Désactiver la bonne réponse actuelle avant d'enregistrer la nouvelle
                foreach ($existingCorrectAnswers as $existing) {
                    $existing->isCorrect = false;
                }
            }
        }
    
        $this->isCorrect = $isCorrect;
    
        // Return the current instance to allow method chaining
        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }
    #[Assert\Callback]
    public function validateCorrectAnswers(ExecutionContextInterface $context)
    {
        if ($this->isCorrect && $this->question && $this->question->getType() === 'choix_unique') {
            $existingCorrectAnswers = $this->question->getReponse()->filter(fn(Reponse $r) => $r->isCorrect());
    
            if (count($existingCorrectAnswers) > 0 && !$existingCorrectAnswers->contains($this))
            {
                $context->buildViolation('Une question à choix unique ne peut avoir qu’une seule bonne réponse.')
                    ->atPath('isCorrect')
                    ->addViolation();
            }
    }    
}
}