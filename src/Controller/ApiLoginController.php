<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ApiLoginController extends AbstractController
{
    public function __construct(
        private TokenRepository $tokenRepository,
        private EntityManagerInterface $entityManager) {}

    /**
     * @throws RandomException
     */
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'Invalid credentials.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->tokenRepository->findOneBy(['owner' => $user], ['expiredAt' => 'DESC']);

        if (!$token->isValid()) {
            $token = new Token($user);
            $this->entityManager->persist($token);
            $this->entityManager->flush();
        }

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token->getTokenString(),
        ]);
    }
}
