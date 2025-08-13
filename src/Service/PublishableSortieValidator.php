<?php

namespace App\Service;

use App\Entity\Sortie;

class PublishableSortieValidator
{

    public function validate(Sortie $sortie):array{

        $errors = [];
/**/
        if (!$sortie->getDateHeureDebut()) {
            $errors[] = 'Il manque la date';
        }
        if ( $sortie->getDateHeureDebut() < new \DateTime('+1 minute') ){
            $errors[] = 'La sortie ne peut pas se dérouler dans le passé';
        }

        if ($sortie->getDateLimiteInscription() && $sortie->getDateLimiteInscription() > $sortie->getDateHeureDebut()) {
            $errors[] = 'La date limite d\'inscription ne peut pas être après le début de la sortie';
        }

        if ( $sortie->getDuree()->getTimestamp() <= 0  ){
            $errors[] = 'La Sortie doit avoir une durée';
        }
        if ( !$sortie->getLieu() ){
            $errors[] = 'La Sortie doit avoir un lieu';
        }

        return $errors;
    }

}