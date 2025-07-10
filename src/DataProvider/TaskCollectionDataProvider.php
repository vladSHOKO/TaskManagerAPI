<?php

namespace App\DataProvider;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class TaskCollectionDataProvider implements ProviderInterface
{
    public function __construct(private Security $security, private TaskRepository $taskRepository)
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        return $this->taskRepository->findBy(['owner' => $user]);
    }
}
