<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date d\'upload est obligatoire.')]
    #[Assert\Type(
        type: '\DateTimeInterface',
        message: 'La date d\'upload doit être une date valide.'
    )]
    private ?\DateTimeInterface $uploadAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le chemin du fichier est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le chemin du fichier ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\/\._\-]+$/',
        message: 'Le chemin du fichier contient des caractères non autorisés.'
    )]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du fichier est obligatoire.')]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'Le nom du fichier doit contenir au moins {{ limit }} caractère.',
        maxMessage: 'Le nom du fichier ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9À-ÿ\s\._\-]+$/',
        message: 'Le nom du fichier contient des caractères non autorisés.'
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[Assert\NotNull(message: 'L\'utilisateur propriétaire du média est obligatoire.')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadAt(): ?\DateTimeInterface
    {
        return $this->uploadAt;
    }

    public function setUploadAt(\DateTimeInterface $uploadAt): static
    {
        $this->uploadAt = $uploadAt;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
