<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\NotBlank]
    private int $id;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 200)]
    private string $tokenString;

    #[ORM\ManyToOne(inversedBy: 'tokens')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private User $owner;

    #[ORM\Column]
    #[Assert\NotBlank]
    private \DateTimeImmutable $expiredAt;

    /**
     * @throws RandomException
     */
    public function __construct()
    {
        $this->tokenString = bin2hex(random_bytes(64));
        $this->expiredAt = (new \DateTimeImmutable())->add(new \DateInterval('PT1H'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTokenString(): string
    {
        return $this->tokenString;
    }

    public function setTokenString(string $tokenString): static
    {
        $this->tokenString = $tokenString;

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

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeImmutable $expiredAt): static
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }
}
