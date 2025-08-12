<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            // Message affiché dans le formulaire de login
            throw new CustomUserMessageAccountStatusException('Votre compte est désactivé.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Others checkers if needed
    }
}
