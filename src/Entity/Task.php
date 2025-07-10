<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource(operations: [
    new GetCollection(),
    new Get(security: "object.getOwner() == user"),
    new Post(security: "object.getOwner() == user"),
    new Patch(security: "object.getOwner() == user"),
    new Delete(security: "object.getOwner() == user")
],
    normalizationContext: ['groups' => ['task:read']],
    denormalizationContext: ['groups' => ['task:write']],
    security: "is_granted('ROLE_USER')",

)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task:read'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['task:read', 'task:write'])]
    private string $title;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['task:read', 'task:write'])]
    private string $description;

    #[ORM\Column]
    #[Assert\NotBlank]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups(['task:read', 'task:write'])]
    private \DateTimeImmutable $completedAt;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Groups(['task:read', 'task:write'])]
    private bool $status = false;

    #[ORM\ManyToOne(inversedBy: 'taskCollection')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['task:read', 'task:write'])]
    private User $owner;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): \DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
