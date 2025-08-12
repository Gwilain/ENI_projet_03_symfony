<?php
namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof UserInterface) {
            return false;
        }


        $targetUser = $subject;

        return match ($attribute) {
            self::EDIT =>
                // L'admin peut tout modifier
                in_array('ROLE_ADMIN', $currentUser->getRoles(), true)
                // Ou l'utilisateur modifie son propre profil
                || $currentUser === $targetUser,
            default => false,
        };
    }
}