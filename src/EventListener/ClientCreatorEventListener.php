<?php

namespace App\EventListener;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;

readonly class ClientCreatorEventListener
{
    /**
     * @param Client $client
     * @param PrePersistEventArgs $event
     */
    public function prePersist(Client $client, PrePersistEventArgs $event): void
    {
        $user = $client->getUser();

        if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
            $user->setRoles([User::ROLE_CLIENT]);
            $event->getObjectManager()->persist($user);
        }
    }
}