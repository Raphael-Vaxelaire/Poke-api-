<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['type:lire']],
    denormalizationContext: ['groups' => ['type:ecrire']]
)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['type:lire',])]
    private ?int $id = null;
    

    #[ORM\Column(length: 255,unique: true)]
    #[Groups(['type:lire', 'type:ecrire', 'pokemon:lire'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Pokemon>
     */
    #[ORM\ManyToMany(targetEntity: Pokemon::class, mappedBy: 'type')]
    private Collection $type;

    public function __construct()
    {
        $this->type = new ArrayCollection();
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

    /**
     * @return Collection<int, Pokemon>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Pokemon $type): static
    {
        if (!$this->type->contains($type)) {
            $this->type->add($type);
            $type->addType($this);
        }

        return $this;
    }

    public function removeType(Pokemon $type): static
    {
        if ($this->type->removeElement($type)) {
            $type->removeType($this);
        }

        return $this;
    }
}
