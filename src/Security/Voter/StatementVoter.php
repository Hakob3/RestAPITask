<?php

namespace App\Security\Voter;

use App\Entity\Statement;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StatementVoter extends Voter implements AppVoterInterface
{
    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, self::OPERATIONS)) {
            return false;
        }

        if ($subject !== null && !$subject instanceof Statement) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Statement $statement */
        $statement = $subject;
        $isGrantedAdmin = in_array(User::ROLE_ADMIN, $user->getRoles());

        return match ($attribute) {
            self::PERSONAL => $this->checkOwner($statement, $user) || $isGrantedAdmin,
            self::CREATE => $user->getClient() !== null,
            default => throw new LogicException('This code should not be reached!')
        };
    }

    /**
     * @param Statement $statement
     * @param User $user
     * @return bool
     */
    private function checkOwner(Statement $statement, User $user): bool
    {
        return $user->getClient() !== null && $statement->getClient() === $user->getClient();
    }
}