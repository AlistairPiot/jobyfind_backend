<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RequestBadgeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: RequestBadgeRepository::class)]
class RequestBadge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $requestDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $responseDate = null;

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
}
