<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\DataProvider\CommentCollectionDataProvider;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(security: 'is_granted("ROLE_ADMIN") or object.getAuthor() == user'),
        new Get(security: 'is_granted("ROLE_ADMIN") or object.getAuthor() == user'),
        new Delete(security: 'is_granted("ROLE_ADMIN") or object.getAuthor() == user'),
        new GetCollection(provider: CommentCollectionDataProvider::class),
        new Patch(security: 'is_granted("ROLE_ADMIN") or object.getAuthor() == user')
    ],
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:write']],
    security: "is_granted('ROLE_USER')",
)]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Goups(['comment:read'])]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Goups(['comment:read'])]
    private User $author;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Goups(['comment:read'])]
    private Task $task;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Goups(['comment:read', 'comment:write'])]
    private string $text;

    public function __construct(Security $security)
    {
        $user = $security->getUser();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }
}
