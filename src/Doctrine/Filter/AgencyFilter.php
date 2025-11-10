<?php

namespace App\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Global filter that restricts entities having an "agency" association
 * to the current user's agency.
 */
class AgencyFilter extends SQLFilter
{
    public const PARAM_NAME = 'agency_id';

    /**
     * Returns the SQL query part to add to a query for filtering by agency.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        // Only apply to entities that have an association named "agency"
        if (!$targetEntity->hasAssociation('agency')) {
            return '';
        }

        // If specifically asked to deny all, return contradiction
        try {
            $denyAll = $this->getParameter('deny_all');
            if ($denyAll === '1' || strtolower($denyAll) === 'true') {
                return '1=0';
            }
        } catch (\InvalidArgumentException) {
            // ignore, fall through
        }

        // If the parameter is not set (e.g. unauthenticated, console), do nothing
        try {
            $agencyId = $this->getParameter(self::PARAM_NAME);
        } catch (\InvalidArgumentException) {
            return '';
        }

        // Fetch the join column name for the "agency" association (default is "agency_id")
        $mapping = $targetEntity->getAssociationMapping('agency');
        $joinColumns = $mapping['joinColumns'] ?? [];
        if (empty($joinColumns)) {
            return '';
        }

        $columnName = $joinColumns[0]['name'] ?? 'agency_id';

        // Return SQL snippet; $agencyId is already properly quoted by Doctrine
        return sprintf('%s.%s = %s', $targetTableAlias, $columnName, $agencyId);
    }
}
