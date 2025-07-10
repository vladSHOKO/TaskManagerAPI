<?php

namespace App\Security;

use App\Repository\TokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private TokenRepository $tokenRepository
    ){}

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->tokenRepository->findOneBy(['tokenString' => $accessToken]);

        if (null === $token || !$token->isValid()) {
            throw new BadCredentialsException('Invalid token');
        }

        return new UserBadge($token->getOwner()->getUserIdentifier());
    }
}
