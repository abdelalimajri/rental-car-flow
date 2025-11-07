<?php

namespace App\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

final class GlobalSearchFilter extends AbstractFilter
{
    public function __construct(ManagerRegistry $managerRegistry, ?LoggerInterface $logger = null, array $properties = null)
    {
        parent::__construct($managerRegistry, $logger, $properties);
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if ($property !== 'q') {
            // we only handle the synthetic 'q' parameter here
            return;
        }

        if ($value === null || $value === '') {
            return;
        }

        $fields = $this->properties['fields'] ?? [];
        if (!$fields) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $orX = $queryBuilder->expr()->orX();
        $param = $queryNameGenerator->generateParameterName('q');
        $needle = '%' . mb_strtolower((string) $value) . '%';

        foreach ($fields as $field) {
            // Only ensure the field is mapped on the entity; don't require it in filter properties
            if (!$this->isPropertyMapped($field, $resourceClass)) {
                continue;
            }
            $orX->add(sprintf('LOWER(%s.%s) LIKE :%s', $alias, $field, $param));
        }

        if (count($orX->getParts()) === 0) {
            return;
        }

        $queryBuilder
            ->andWhere($orX)
            ->setParameter($param, $needle);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'q' => [
                'property' => 'q',
                'type' => 'string',
                'required' => false,
                'description' => 'Global, case-insensitive contains search across configured fields.'
            ],
        ];
    }
}
