<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    // Spécifie les groupes de lecture à exposer pour cette ressource
    normalizationContext: ['groups' => ['mission:read']],
    denormalizationContext: ['groups' => ['mission:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete()
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['user.id' => 'exact'])]
#[ORM\Entity(repositoryClass: MissionRepository::class)]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['mission:read','job_application:read'])]
    private ?int $id = null;

    private $tempField;

    #[ORM\Column(length: 255)]
    #[Groups(['mission:read','job_application:read','mission:write'])]
    #[Assert\NotBlank(message: 'Le nom de la mission est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le nom de la mission doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom de la mission ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'mission')]
    #[ApiProperty(readableLink: true, writableLink: true)] // Indique que c'est une relation URL
    #[Groups(['mission:read', 'job_application:read', 'user:read', 'mission:write'])]
    #[Assert\NotNull(message: 'L\'utilisateur créateur de la mission est obligatoire.')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'missions')]
    #[ApiProperty(readableLink: true, writableLink: true)] // Idem ici
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['mission:read','job_application:read', 'mission:write'])]
    #[Assert\NotNull(message: 'Le type de contrat est obligatoire.')]
    private ?Type $type = null;

    /**
     * @var Collection<int, JobApplication>
     */
    #[ORM\ManyToMany(targetEntity: JobApplication::class, inversedBy: 'missions')]
    private Collection $jobApplication;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['mission:read','mission:write'])]
    #[Assert\NotBlank(message: 'La description de la mission est obligatoire.')]
    #[Assert\Length(
        min: 10,
        max: 5000,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $description = null;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class)]
    #[ORM\JoinTable(name: 'mission_skill')]
    #[ApiProperty(readableLink: true, writableLink: true)]
    #[Groups(['mission:read', 'mission:write'])]
    private Collection $skills;

    public function __construct()
    {
        $this->jobApplication = new ArrayCollection();
        $this->skills = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, JobApplication>
     */
    public function getJobApplication(): Collection
    {
        return $this->jobApplication;
    }

    public function addJobApplication(JobApplication $jobApplication): static
    {
        if (!$this->jobApplication->contains($jobApplication)) {
            $this->jobApplication->add($jobApplication);
        }

        return $this;
    }

    public function removeJobApplication(JobApplication $jobApplication): static
    {
        $this->jobApplication->removeElement($jobApplication);

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

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }
}
