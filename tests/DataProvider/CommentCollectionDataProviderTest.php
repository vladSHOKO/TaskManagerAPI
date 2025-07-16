<?php

namespace App\Tests\DataProvider;

use ApiPlatform\Metadata\Operation;
use App\DataProvider\CommentCollectionDataProvider;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CommentCollectionDataProviderTest extends WebTestCase
{
    private Security $security;

    private CommentRepository $commentRepository;

    private CommentCollectionDataProvider $provider;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->provider = new CommentCollectionDataProvider($this->security, $this->commentRepository);
    }

    /**
     * @throws Exception
     */
    public function testThrowsAccessDeniedHttpExceptionWhenUserNotLoggedIn(): void
    {
        $this->security->method('getUser')->willReturn(null);

        $operation = $this->createMock(Operation::class);

        $this->expectException(AccessDeniedHttpException::class);
        $this->provider->provide($operation);
    }

    /**
     * @throws Exception
     */
    public function testThrowsAccessDeniedHttpExceptionWhenUserDontHaveAdminRights(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $this->security->method('getUser')->willReturn($user);

        $operation = $this->createMock(Operation::class);

        $this->expectException(AccessDeniedHttpException::class);
        $this->provider->provide($operation);
    }

    /**
     * @throws Exception
     */
    public function testReturnsCommentCollection(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn(['ROLE_ADMIN']);

        $this->security->method('getUser')->willReturn($user);

        $operation = $this->createMock(Operation::class);

        $expectedComments = [new Comment(), new Comment(), new Comment()];

        $this->commentRepository->method('findAll')->willReturn($expectedComments);

        $comments = $this->provider->provide($operation);

        $this->assertSame($expectedComments, $comments);
    }
}
