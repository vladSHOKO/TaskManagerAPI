<?php

namespace App\Tests\Unit\Processor;

use ApiPlatform\Metadata\Operation;
use App\Entity\Comment;
use App\Entity\User;
use App\Processor\CommentPostProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Negotiation\Exception\InvalidArgument;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CommentPostProcessorTest extends WebTestCase
{
    private Security $security;

    private EntityManagerInterface $entityManager;

    private CommentPostProcessor $commentPostProcessor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->commentPostProcessor = new CommentPostProcessor($this->security, $this->entityManager);
    }

    /**
     * @throws Exception
     */
    public function testThrowsAccessDeniedHttpExceptionWhenUserNotLoggedIn(): void
    {
        $this->security->method('getUser')->willReturn(null);

        $operation = $this->createMock(Operation::class);
        $comment = new Comment();

        $this->expectException(AccessDeniedHttpException::class);

        $this->commentPostProcessor->process($comment, $operation);
    }

    public function testThrowsInvalidArgumentExceptionWhenDataIsNotComment(): void
    {
        $user = $this->createMock(User::class);
        $this->security->method('getUser')->willReturn($user);
        $operation = $this->createMock(Operation::class);

        $this->expectException(InvalidArgument::class);

        $this->commentPostProcessor->process(new \stdClass(), $operation);
    }

    /**
     * @throws Exception
     */
    public function testProcessSetAuthorAndPersistComment(): void
    {
        $user = $this->createMock(User::class);
        $this->security->method('getUser')->willReturn($user);

        $comment = $this->createMock(Comment::class);
        $comment->expects($this->once())->method('setAuthor')->with($user);

        $this->entityManager->expects($this->once())->method('persist')->with($comment);
        $this->entityManager->expects($this->once())->method('flush');

        $operation = $this->createMock(Operation::class);

        $result = $this->commentPostProcessor->process($comment, $operation);

        $this->assertSame($comment, $result);
    }
}
