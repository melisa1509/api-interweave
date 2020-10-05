<?php

namespace App\Event\Listener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    /**
    * Agregar data adicional al token de JWT
    *
    * @param JWTCreatedEvent $event
    *
    * @return void
    */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        /** @var $user \AppBundle\Entity\User */
        $user = $event->getUser();

        

        // Inyectamos data al payload
        $payload = array_merge(
            $event->getData(),
            [
            'roles' => $user->getRoles()
            ]
        );

        $event->setData($payload);
    }
}