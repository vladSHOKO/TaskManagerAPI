<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CommentRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CommentCollectionDataProvider implements ProviderInterface
{
    public function __construct(private Security $security, private CommentRepository $commentRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        if (empty($user) || !in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        return $this->commentRepository->findAll();
    }
}
