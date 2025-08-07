<?php

namespace App\Security\Voter;

use App\Entity\Etat;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SortieVoter extends Voter
{
    public const EDIT = 'SORTIE_EDIT';
    public const VIEW = 'SORTIE_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        $sortie = $subject;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $sortie->getOrganisateur() === $user
                    && $sortie->getEtat()->getCode() === Etat::CODE_EN_CREATION;

            case self::VIEW:
                return $sortie->getOrganisateur() === $user
                    || in_array($sortie->getEtat()->getCode(), [ Etat::CODE_OUVERTE,  Etat::CODE_EN_COURS], true);
        }


        return false;
    }
}
