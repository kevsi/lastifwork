<?php

namespace App\Service;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JWTService
{
    private $jwtManager;
    private $tokenStorage;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Crée un token JWT pour un utilisateur
     */
    public function createToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }

    /**
     * Récupère l'utilisateur courant à partir du token
     */
    public function getCurrentUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    /**
     * Récupère les informations à partir du token
     */
    public function getTokenData(): array
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return [];
        }

        return [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "roles" => $user->getRoles(),
        ];
    }
}
