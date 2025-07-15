<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Negotiation\Exception\InvalidArgument;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class CommentPostProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if (empty($user)) {
            throw new AccessDeniedHttpException();
        }

        if (!$data instanceof Comment) {
            throw new InvalidArgument();
        }

        $data->setAuthor($user);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
