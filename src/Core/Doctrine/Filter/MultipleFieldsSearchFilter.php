<?php

namespace App\Core\Doctrine\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class MultipleFieldsSearchFilter extends AbstractContextAwareFilter
{
    private string $searchParameterName;
    private array $nestedAliases;

    public function __construct(ManagerRegistry $managerRegistry, ?RequestStack $requestStack = null, LoggerInterface $logger = null, array $properties = null, NameConverterInterface $nameConverter = null)
    {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);

        $this->searchParameterName = 'q';
        $this->nestedAliases = [];
    }

    // @phpstan-ignore-next-line
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = []): void
    {
        if (null === $value || $property !== $this->searchParameterName) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $properties = $this->getProperties();

        // handle joins
        if (\is_array($properties)) {
            foreach ($properties as $prop => $propValue) {
                if ($this->isPropertyNested($prop, $resourceClass)) {
                    [$nestedPropertyAlias] = $this->addJoinsForNestedProperty($prop, $alias, $queryBuilder, $queryNameGenerator, $resourceClass);
                    $this->nestedAliases[$prop] = $nestedPropertyAlias;
                }
            }
        }

        $this->addWhere($queryBuilder, $value, $queryNameGenerator->generateParameterName($property));
    }

    private function addWhere(QueryBuilder $queryBuilder, string $word, string $parameterName): void
    {
        $alias = $queryBuilder->getRootAliases()[0];

        // Build OR expression
        $orExp = $queryBuilder->expr()->orX();
        $properties = $this->getProperties();
        if (\is_array($properties)) {
            foreach ($properties as $prop => $value) {
                $propArr = explode('.', $prop);
                if (\count($propArr) < 2) {
                    $orExp->add($queryBuilder->expr()->like('LOWER(' . $alias . '.' . $prop . ')', ':' . $parameterName));
                } else {
                    // handle subproperties
                    $lastKey = array_key_last($propArr); // get always last from ['entity','parentProperty','property']
                    if (null !== $subProperty = \array_key_exists($lastKey, $propArr) ? $propArr[$lastKey] : null) {
                        if (\array_key_exists($prop, $this->nestedAliases)) {
                            // content is already lowered
                            if ('content' === $subProperty) {
                                $orExp->add($queryBuilder->expr()->like($this->nestedAliases[$prop] . '.' . $subProperty, ':' . $parameterName));
                            } else {
                                $orExp->add($queryBuilder->expr()->like('LOWER(' . $this->nestedAliases[$prop] . '.' . $subProperty . ')', ':' . $parameterName));
                            }
                        }
                    }
                }
            }
        }

        $queryBuilder
            ->andWhere('(' . $orExp . ')')
            ->setParameter($parameterName, '%' . strtolower($word) . '%')
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        $props = $this->getProperties();
        if (null === $props) {
            throw new InvalidArgumentException('Properties must be specified');
        }

        return [
            $this->searchParameterName => [
                'property' => implode(', ', array_keys($props)),
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Selects entities where each search term is found somewhere in at least one of the specified properties',
                ],
            ],
        ];
    }
}
