<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserCreatorEventListener
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    /**
     * @param User $user
     * @param PrePersistEventArgs $event
     */
    public function prePersist(User $user, PrePersistEventArgs $event): void
    {
        $password = $user->getPassword();
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
    }
}