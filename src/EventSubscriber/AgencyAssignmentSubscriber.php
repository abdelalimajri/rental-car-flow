<?php

namespace App\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Bundle\SecurityBundle\Security; // corrigé
use App\Entity\User;

#[AsDoctrineListener(event: 'prePersist', priority: 0)]
class AgencyAssignmentSubscriber
{
    public function __construct(private readonly Security $security) {}

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        // Vérifie si l'entité a une propriété 'agency'
        if (!\property_exists($entity, 'agency')) {
            return; // Rien à faire
        }

        // Si déjà définie, ne pas écraser
        if ($entity->agency !== null) {
            return;
        }

        // Récupérer l'utilisateur courant
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return; // Pas d'utilisateur ou pas du bon type
        }

        // Si l'utilisateur n'a pas d'agence (super admin éventuellement), on ne définit pas
        if ($user->agency === null) {
            return;
        }

        // Assigner l'agence de l'utilisateur
        $entity->agency = $user->agency;
    }
}
