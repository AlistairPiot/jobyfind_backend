<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'skill')]
    private Collection $users;

    /**
     * @var Collection<int, SkillCategory>
     */
    #[ORM\ManyToMany(targetEntity: SkillCategory::class, inversedBy: 'skills')]
    private Collection $skillCategory;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->skillCategory = new ArrayCollection();
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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addSkill($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeSkill($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, SkillCategory>
     */
    public function getSkillCategory(): Collection
    {
        return $this->skillCategory;
    }

    public function addSkillCategory(SkillCategory $skillCategory): static
    {
        if (!$this->skillCategory->contains($skillCategory)) {
            $this->skillCategory->add($skillCategory);
        }

        return $this;
    }

    public function removeSkillCategory(SkillCategory $skillCategory): static
    {
        $this->skillCategory->removeElement($skillCategory);

        return $this;
    }
}
