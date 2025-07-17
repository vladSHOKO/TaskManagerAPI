<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class TaskCollectionDataProvider implements ProviderInterface
{
    public function __construct(private Security $security, private TaskRepository $taskRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        if (empty($user)) {
            throw new AccessDeniedHttpException();
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->taskRepository->findAll();
        }

        return $this->taskRepository->findBy(['owner' => $user]);
    }
}
