<?php
//src/EventListenner/JWTResponseListenner.php

namespace App\EventListenner;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTResponseListener
{
    public function onAuthenticationSuccess(
        AuthenticationSuccessEvent $event
    ): void {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // Ajout d'informations supplémentaires au payload
        $data["user"] = [
            //"id" => $user->getId(),
            //"email" => $user->getEmail(),
            //"firstName" => $user->getFirstName(),
            //"lastName" => $user->getLastName(),
            "roles" => $user->getRoles(),
        ];

        $event->setData($data);
    }
}
