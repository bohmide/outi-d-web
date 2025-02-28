<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Form\QuestionType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;



#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La question ne peut pas être vide.")]
    #[Assert\Length(min: 5, minMessage: "La question doit comporter au moins {{ limit }} caractères.")]
    private ?string $question = null;
 
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le type de question est obligatoire.")]
    #[Assert\Choice(choices: ['choix_unique', 'choix_multiple'], message: "Le type doit être 'choix_unique' ou 'choix_multiple'.")]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'questions', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    /**
     * @var Collection<int, Reponse>
     */
    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'question', cascade :['remove'])]
    private Collection $reponse;

    public function __construct()
    {
        $this->reponse = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

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

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponse(): Collection
    {
        return $this->reponse;
    }

    public function addReponse(Reponse $reponse): static
    {
        if (!$this->reponse->contains($reponse)) {
            $this->reponse->add($reponse);
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static
    {
        if ($this->reponse->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }
    
    #[Assert\Callback]
    public function validateCorrectAnswers(ExecutionContextInterface $context)
    {
        $correctAnswers = $this->getReponse()->filter(fn(Reponse $reponse) => $reponse->isCorrect());
    
        if ($this->type === 'choix_unique' && count($correctAnswers) > 1) {
            $context->buildViolation('Une seule réponse correcte est autorisée pour un choix unique.')
                ->atPath('reponses')
                ->addViolation();
        }
    
        if ($this->type === 'choix_multiple' && count($correctAnswers) < 1) {
            $context->buildViolation('Au moins une bonne réponse est requise pour un choix multiple.')
                ->atPath('reponses')
                ->addViolation();
        }
    }    

}