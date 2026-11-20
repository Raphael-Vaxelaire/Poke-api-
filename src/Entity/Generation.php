<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GenerationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GenerationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['generation:lire']],
    denormalizationContext: ['groups' => ['generation:ecrire']]
)]
class Generation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['generation:lire'])]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Groups(['generation:lire', 'generation:ecrire', 'pokemon:lire'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\UniqueEntity]
    private ?int $number = null;

    /**
     * @var Collection<int, Pokemon>
     */
    #[ORM\OneToMany(targetEntity: Pokemon::class, mappedBy: 'generation')]
    private Collection $generation;

    public function __construct()
    {
        $this->generation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection<int, Pokemon>
     */
    public function getGeneration(): Collection
    {
        return $this->generation;
    }

    public function addGeneration(Pokemon $generation): static
    {
        if (!$this->generation->contains($generation)) {
            $this->generation->add($generation);
            $generation->setGeneration($this);
        }

        return $this;
    }

    public function removeGeneration(Pokemon $generation): static
    {
        if ($this->generation->removeElement($generation)) {
            // set the owning side to null (unless already changed)
            if ($generation->getGeneration() === $this) {
                $generation->setGeneration(null);
            }
        }

        return $this;
    }
}
