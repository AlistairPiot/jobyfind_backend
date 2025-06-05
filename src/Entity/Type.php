<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    normalizationContext: ['groups' => ['type:read']],
    denormalizationContext: ['groups' => ['type:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(security: "is_granted('ROLE_COMPANY')"),
        new Delete(security: "is_granted('ROLE_COMPANY')")
    ]
)]
#[ORM\Entity(repositoryClass: TypeRepository::class)]
#[UniqueEntity(
    fields: ['name'],
    message: 'Un type de contrat avec ce nom existe déjà.'
)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['mission:read', 'job_application:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['mission:read', 'job_application:read', 'type:read', 'type:write'])]
    #[Assert\NotBlank(message: 'Le nom du type de contrat est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom du type de contrat doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom du type de contrat ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ0-9\s\-\_\/]+$/',
        message: 'Le nom du type de contrat ne peut contenir que des lettres, chiffres, espaces, tirets, underscores et slashes.'
    )]
    private ?string $name = null;

    /**
     * @var Collection<int, Mission>
     */
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Mission::class)]
    private Collection $missions;

    public function __construct()
    {
        $this->missions = new ArrayCollection();
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
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): static
    {
        if (!$this->missions->contains($mission)) {
            $this->missions[] = $mission;
            $mission->setType($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): static
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getType() === $this) {
                $mission->setType(null);
            }
        }

        return $this;
    }
}
