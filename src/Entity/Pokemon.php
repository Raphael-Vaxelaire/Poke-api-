<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['pokemon:lire']],
    denormalizationContext: ['groups' => ['pokemon:ecrire']]
)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(unique: true)]
    #[Groups(['pokemon:lire'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['pokemon:lire', 'pokemon:ecrire'])]
    #[Assert\NotBlank]
    #[Assert\Length(255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pokemon:lire', 'pokemon:ecrire'])]
    #[Assert\Positive]
    private ?int $height = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pokemon:lire', 'pokemon:ecrire'])]
    #[Assert\Positive]
    private ?int $weight = null;

    #[ORM\Column]
    #[Groups(['pokemon:lire', 'pokemon:ecrire'])]
    #[Assert\NotNull]
    private ?bool $originalForm = null;

    #[ORM\ManyToOne(inversedBy: 'generation')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['pokemon:lire', 'pokemon:ecrire'])]
    private ?Generation $generation = null;

    /**
     * @var Collection<int, Type>
     */
    #[ORM\ManyToMany(targetEntity: Type::class, inversedBy: 'type')]
    #[Groups(['pokemon:lire', 'pokemon:ecrire'])]
    private Collection $type;

    #[ORM\Column(unique: true)]
    #[Groups(['pokemon:lire'])]
    private ?int $pokemonId = null;

    public function __construct()
    {
        $this->type = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function isOriginalForm(): ?bool
    {
        return $this->originalForm;
    }

    public function setOriginalForm(bool $originalForm): static
    {
        $this->originalForm = $originalForm;

        return $this;
    }

    public function getGeneration(): ?Generation
    {
        return $this->generation;
    }

    public function setGeneration(?Generation $generation): static
    {
        $this->generation = $generation;

        return $this;
    }

    /**
     * @return Collection<int, Type>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Type $type): static
    {
        if (!$this->type->contains($type)) {
            $this->type->add($type);
        }

        return $this;
    }

    public function removeType(Type $type): static
    {
        $this->type->removeElement($type);

        return $this;
    }

    public function getPokemonId(): ?int
    {
        return $this->pokemonId;
    }

    public function setPokemonId(int $pokemonId): static
    {
        $this->pokemonId = $pokemonId;

        return $this;
    }
}
