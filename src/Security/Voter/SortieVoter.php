<?php

namespace App\Security\Voter;

use App\Entity\Sortie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SortieVoter extends Voter
{
    public const EDIT = 'SORTIE_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Sortie $sortie */
        $sortie = $subject;

        // Vérification logique : peut modifier si organisateur et en création
        return $sortie->getOrganisateur() === $user
            && $sortie->getEtat()->getLibelle() === 'En création';
    }
}