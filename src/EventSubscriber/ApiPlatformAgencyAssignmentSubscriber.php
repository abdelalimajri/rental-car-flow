<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;

/**
 * Assigne l'agence de l'utilisateur connecté aux nouvelles entités (POST) avant la validation.
 */
class ApiPlatformAgencyAssignmentSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Security $security) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreValidate', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function onPreValidate(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getMethod() !== 'POST') {
            return; // uniquement à la création
        }

        $entity = $event->getControllerResult();
        if (!\is_object($entity)) {
            return;
        }
        // Vérifie présence propriété agency
        if (!\property_exists($entity, 'agency')) {
            return;
        }
        // Ne rien faire si déjà renseignée
        if ($entity->agency !== null) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }
        if ($user->agency === null) {
            return; // super admin sans agence
        }

        // Assigner avant validation pour que UniqueEntity(fields: ['x','agency']) fonctionne
        $entity->agency = $user->agency;
    }
}

