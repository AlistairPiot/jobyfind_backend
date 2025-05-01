<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource]
#[ORM\Entity(repositoryClass: MissionRepository::class)]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'mission')]
    #[ApiProperty(readableLink: true, writableLink: true)] // Indique que c'est une relation URL
    private ?User $user = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ApiProperty(readableLink: true, writableLink: true)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    /**
     * @var Collection<int, JobApplication>
     */
    #[ORM\ManyToMany(targetEntity: JobApplication::class, inversedBy: 'missions')]
    private Collection $jobApplication;

    public function __construct()
    {
        $this->jobApplication = new ArrayCollection();
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
}
