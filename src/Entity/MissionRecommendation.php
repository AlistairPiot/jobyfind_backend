<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MissionRecommendationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['mission_recommendation:read']],
    denormalizationContext: ['groups' => ['mission_recommendation:write']]
)]
#[ORM\Entity(repositoryClass: MissionRecommendationRepository::class)]
class MissionRecommendation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['mission_recommendation:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Mission::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['mission_recommendation:read', 'mission_recommendation:write'])]
    private ?Mission $mission = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['mission_recommendation:read', 'mission_recommendation:write'])]
    private ?User $student = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['mission_recommendation:read', 'mission_recommendation:write'])]
    private ?User $school = null;

    #[ORM\Column]
    #[Groups(['mission_recommendation:read', 'mission_recommendation:write'])]
    private ?\DateTimeImmutable $recommendedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): static
    {
        $this->mission = $mission;

        return $this;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getSchool(): ?User
    {
        return $this->school;
    }

    public function setSchool(?User $school): static
    {
        $this->school = $school;

        return $this;
    }

    public function getRecommendedAt(): ?\DateTimeImmutable
    {
        return $this->recommendedAt;
    }

    public function setRecommendedAt(\DateTimeImmutable $recommendedAt): static
    {
        $this->recommendedAt = $recommendedAt;

        return $this;
    }
} 