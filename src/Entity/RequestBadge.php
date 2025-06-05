<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RequestBadgeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['request_badge:read']],
    denormalizationContext: ['groups' => ['request_badge:write']]
)]
#[ORM\Entity(repositoryClass: RequestBadgeRepository::class)]
class RequestBadge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['request_badge:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['request_badge:read', 'request_badge:write'])]
    #[Assert\NotNull(message: 'La date de demande est obligatoire.')]
    #[Assert\Type(
        type: '\DateTimeImmutable',
        message: 'La date de demande doit être une date valide.'
    )]
    private ?\DateTimeImmutable $requestDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['request_badge:read', 'request_badge:write'])]
    #[Assert\Type(
        type: '\DateTimeImmutable',
        message: 'La date de réponse doit être une date valide.'
    )]
    #[Assert\Expression(
        "this.getResponseDate() === null or this.getResponseDate() >= this.getRequestDate()",
        message: 'La date de réponse ne peut pas être antérieure à la date de demande.'
    )]
    private ?\DateTimeImmutable $responseDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['request_badge:read', 'request_badge:write'])]
    #[Assert\Choice(
        choices: ['PENDING', 'APPROVED', 'REJECTED'],
        message: 'Le statut {{ value }} n\'est pas valide. Les valeurs autorisées sont : PENDING, APPROVED, REJECTED.'
    )]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['request_badge:read', 'request_badge:write'])]
    #[Assert\NotNull(message: 'L\'utilisateur demandeur est obligatoire.')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['request_badge:read', 'request_badge:write'])]
    #[Assert\NotNull(message: 'L\'école est obligatoire.')]
    #[Assert\Expression(
        "this.getSchool() !== this.getUser()",
        message: 'L\'utilisateur ne peut pas faire une demande de badge à lui-même.'
    )]
    private ?User $school = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestDate(): ?\DateTimeImmutable
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeImmutable $requestDate): static
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    public function getResponseDate(): ?\DateTimeImmutable
    {
        return $this->responseDate;
    }

    public function setResponseDate(?\DateTimeImmutable $responseDate): static
    {
        $this->responseDate = $responseDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

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

    public function getSchool(): ?User
    {
        return $this->school;
    }

    public function setSchool(?User $school): static
    {
        $this->school = $school;

        return $this;
    }
}
