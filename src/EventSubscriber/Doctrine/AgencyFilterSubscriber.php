<?php

namespace App\EventSubscriber\Doctrine;

use App\Doctrine\Filter\AgencyFilter;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Enables and configures the Doctrine agency filter per HTTP request.
 */
class AgencyFilterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly Security $security,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Use CONTROLLER event to ensure authentication (security token) is initialized
            KernelEvents::CONTROLLER => ['onKernelController', 0],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $em = $this->registry->getManager();
        if (!$em->isOpen()) {
            return;
        }

        $filters = $em->getFilters();
        if (!$filters->has('agency_filter')) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            // No authenticated user -> disable to avoid stale parameters
            if ($filters->isEnabled('agency_filter')) {
                $filters->disable('agency_filter');
            }
            return;
        }

        // Super admin can see everything -> disable the filter
        if ($user->isSuperAdmin()) {
            if ($filters->isEnabled('agency_filter')) {
                $filters->disable('agency_filter');
            }
            return;
        }

        // For agency users, ensure filter is enabled and parameter set
        $agency = $user->agency;
        if (null !== $agency && null !== $agency->id) {
            $filter = $filters->enable('agency_filter');
            // Ulid string format matches column type
            $filter->setParameter(AgencyFilter::PARAM_NAME, (string) $agency->id->toRfc4122());
            return;
        }

        // Fallback: no agency and not super admin -> safest is to deny all
        $filter = $filters->enable('agency_filter');
        $filter->setParameter('deny_all', '1');
    }
}
