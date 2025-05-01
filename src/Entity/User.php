<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource(
    normalizationContext: ['groups' => ['user:read']]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['mission:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['mission:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $locationCity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $locationRegion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $locationCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameCompany = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactEmail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $badge = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $media;

    /**
     * @var Collection<int, Mission>
     */
    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'user')]
    private Collection $mission;

    /**
     * @var Collection<int, JobApplication>
     */
    #[ORM\OneToMany(targetEntity: JobApplication::class, mappedBy: 'user')]
    private Collection $jobApplication;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'users')]
    private Collection $skill;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?RequestBadge $requestBadge = null;

    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->mission = new ArrayCollection();
        $this->jobApplication = new ArrayCollection();
        $this->skill = new ArrayCollection();
        $this->createdAt = new \DateTime();
        // Par défaut, l'utilisateur a un rôle de "ROLE_USER"
        $this->roles[] = 'ROLE_USER';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLocationCity(): ?string
    {
        return $this->locationCity;
    }

    public function setLocationCity(?string $locationCity): static
    {
        $this->locationCity = $locationCity;

        return $this;
    }

    public function getLocationRegion(): ?string
    {
        return $this->locationRegion;
    }

    public function setLocationRegion(?string $locationRegion): static
    {
        $this->locationRegion = $locationRegion;

        return $this;
    }

    public function getLocationCode(): ?string
    {
        return $this->locationCode;
    }

    public function setLocationCode(?string $locationCode): static
    {
        $this->locationCode = $locationCode;

        return $this;
    }

    public function getNameCompany(): ?string
    {
        return $this->nameCompany;
    }

    public function setNameCompany(?string $nameCompany): static
    {
        $this->nameCompany = $nameCompany;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBadge(): ?\DateTimeImmutable
    {
        return $this->badge;
    }

    public function setBadge(\DateTimeImmutable $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setUser($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getUser() === $this) {
                $medium->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMission(): Collection
    {
        return $this->mission;
    }

    public function addMission(Mission $mission): static
    {
        if (!$this->mission->contains($mission)) {
            $this->mission->add($mission);
            $mission->setUser($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): static
    {
        if ($this->mission->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getUser() === $this) {
                $mission->setUser(null);
            }
        }

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
            $jobApplication->setUser($this);
        }

        return $this;
    }

    public function removeJobApplication(JobApplication $jobApplication): static
    {
        if ($this->jobApplication->removeElement($jobApplication)) {
            // set the owning side to null (unless already changed)
            if ($jobApplication->getUser() === $this) {
                $jobApplication->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkill(): Collection
    {
        return $this->skill;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skill->contains($skill)) {
            $this->skill->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skill->removeElement($skill);

        return $this;
    }

    public function getRequestBadge(): ?RequestBadge
    {
        return $this->requestBadge;
    }

    public function setRequestBadge(?RequestBadge $requestBadge): static
    {
        $this->requestBadge = $requestBadge;

        return $this;
    }

    /**
     * ⚠️ Nouvelle méthode obligatoire depuis Symfony 5.3
     * Cette méthode remplace getEmail()
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // Getter et setter pour $roles
    public function getRoles(): array
    {
        return array_unique($this->roles); // Éviter les doublons
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Non utilisé, mais requis par l'interface UserInterface
    }

}
