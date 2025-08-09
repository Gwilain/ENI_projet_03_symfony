<?php

namespace App\Security\Voter;

use App\Entity\Etat;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\Clock\now;

final class SortieVoter extends Voter
{
    public const EDIT = 'SORTIE_EDIT';
    public const VIEW = 'SORTIE_VIEW';
    public const ENROLL = 'SORTIE_ENROLL';
    public const WITHDRAW = 'SORTIE_WITHDRAW';
    public const CANCELABLE = 'SORTIE_CANCELABLE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::ENROLL, self::WITHDRAW, self::CANCELABLE])
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
                    || in_array($sortie->getEtat()->getCode(), [ Etat::CODE_OUVERTE,  Etat::CODE_EN_COURS, Etat::CODE_CLOTUREE], true);

            case self::ENROLL:
                return
                    !in_array($user, $sortie->getParticipants()->toArray(), true)
                    && $sortie->getEtat()->getCode() === Etat::CODE_OUVERTE
                    && $sortie->getDateLimiteInscription() > new \DateTimeImmutable('now')
                    && count($sortie->getParticipants()) < $sortie->getNbInscriptionMax();

            case self::WITHDRAW:
                return in_array($user, $sortie->getParticipants()->toArray(), true)
                    && ($sortie->getEtat()->getCode() === Etat::CODE_OUVERTE
                    || $sortie->getEtat()->getCode() === Etat::CODE_CLOTUREE)
                    && $sortie->getEtat()->getCode() !== Etat::CODE_ANNULEE
                    && $sortie->getDateHeureDebut() > new \DateTimeImmutable('now');

            case self::CANCELABLE:
                return $sortie->getOrganisateur() === $user
                    && $sortie->getEtat()->getCode() === Etat::CODE_OUVERTE;
        }

        return false;
    }
}
