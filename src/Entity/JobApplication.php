<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\JobApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['job_application:read']],
    denormalizationContext: ['groups' => ['job_application:write']],
)]
#[ApiFilter(SearchFilter::class, properties: ['user.id' => 'exact', 'missions.id' => 'exact'])]
#[ORM\Entity(repositoryClass: JobApplicationRepository::class)]
class JobApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['job_application:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['job_application:read', 'job_application:write'])]
    #[Assert\NotBlank(message: 'Le statut de la candidature est obligatoire.')]
    #[Assert\Choice(
        choices: ['PENDING', 'ACCEPTED', 'REJECTED', 'CANCELLED'],
        message: 'Le statut {{ value }} n\'est pas valide. Les valeurs autorisées sont : PENDING, ACCEPTED, REJECTED, CANCELLED.'
    )]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(['job_application:read', 'job_application:write'])]
    #[Assert\NotNull(message: 'La date de candidature est obligatoire.')]
    #[Assert\Type(
        type: '\DateTimeImmutable',
        message: 'La date de candidature doit être une date valide.'
    )]
    private ?\DateTimeImmutable $DateApplied = null;

    #[ORM\ManyToOne(inversedBy: 'jobApplication')]
    #[Groups(['job_application:read', 'job_application:write'])]
    #[Assert\NotNull(message: 'L\'utilisateur candidat est obligatoire.')]
    private ?User $user = null;

    /**
     * @var Collection<int, Mission>
     */
    #[ORM\ManyToMany(targetEntity: Mission::class, mappedBy: 'jobApplication')]
    #[Groups(['job_application:read', 'job_application:write'])]
    #[Assert\Count(
        min: 1,
        minMessage: 'Au moins une mission doit être associée à la candidature.'
    )]
    private Collection $missions;

    public function __construct()
    {
        $this->missions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateApplied(): ?\DateTimeImmutable
    {
        return $this->DateApplied;
    }

    public function setDateApplied(\DateTimeImmutable $DateApplied): static
    {
        $this->DateApplied = $DateApplied;

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
            $this->missions->add($mission);
            $mission->addJobApplication($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): static
    {
        if ($this->missions->removeElement($mission)) {
            $mission->removeJobApplication($this);
        }

        return $this;
    }
}
