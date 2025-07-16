<?php

namespace App\Tests\Unit\DataProvider;

use ApiPlatform\Metadata\Operation;
use App\DataProvider\TaskCollectionDataProvider;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TaskCollectionDataProviderTest extends WebTestCase
{
    private Security $security;

    private TaskRepository $taskRepository;

    private TaskCollectionDataProvider $provider;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->provider = new TaskCollectionDataProvider($this->security, $this->taskRepository);
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionAccessDeniedHttpExceptionWhenUserNotLoggedIn(): void
    {
        $this->security->method('getUser')->willReturn(null);

        $operation = $this->createMock(Operation::class);

        $this->expectException(AccessDeniedHttpException::class);
        $this->provider->provide($operation);
    }

    /**
     * @throws Exception
     */
    public function testReturnsAllTasksForAdminWithoutChanging(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn(['ROLE_ADMIN']);

        $this->security->method('getUser')->willReturn($user);

        $expectedTasks = [new Task(), new Task(), new Task()];
        $this->taskRepository->method('findAll')->willReturn($expectedTasks);

        $operation = $this->createMock(Operation::class);

        $tasks = $this->provider->provide($operation);
        $this->assertSame($expectedTasks, $tasks);
    }

    /**
     * @throws Exception
     */
    public function testReturnsOnlyUsersTasksForNotAdmin(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $this->security->method('getUser')->willReturn($user);

        $expectedTasks = ['userTask1'];
        $this->taskRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['owner' => $user])
            ->willReturn($expectedTasks);

        $operation = $this->createMock(Operation::class);

        $result = $this->provider->provide($operation);

        $this->assertSame($expectedTasks, $result);
    }
}
