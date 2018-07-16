<?php

namespace DataMapper\Strategy;

use DataMapper\Exception\InvalidArgumentException;
use DataMapper\Hydrator\HydratorInterface;
use DataMapper\MappingRegistry\RelationsRegistryInterface;

/**
 * Class MultiCollectionStrategy
 */
class CollectionStrategy implements StrategyInterface
{
    /**
     * @var RelationsRegistryInterface
     */
    private $mappingRegistry;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * CollectionStrategy constructor.
     *
     * @param HydratorInterface          $hydrator
     * @param RelationsRegistryInterface $mappingRegistry
     */
    public function __construct(HydratorInterface $hydrator, RelationsRegistryInterface $mappingRegistry)
    {
        $this->mappingRegistry = $mappingRegistry;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($value, $context)
    {
        if (!\is_array($value) || !\is_array($context)) {
            return $value;
        }

        [$contextClass, $propertyName] = $context;

        if (!\is_string($contextClass) || !\class_exists($contextClass)) {
            throw new InvalidArgumentException('$context - argument must be exists class name');
        }

        $hasRelation = $this->mappingRegistry->hasRegisteredRelation($propertyName, $contextClass);

        if (!$hasRelation) {
            return $value;
        }

        $relationTargetClass = $this
            ->mappingRegistry
            ->getRegisteredRelation($propertyName, $contextClass);

        $hasMultiRelation = $this
            ->mappingRegistry
            ->hasRegisteredMultiRelation($propertyName, $contextClass);

        if (!$hasMultiRelation) {
            return $this->hydrator->hydrate($value, $relationTargetClass);
        }

        return \array_map(
            function ($element) use ($relationTargetClass) {
                return $this->hydrator->hydrate($element, $relationTargetClass);
            },
            $value
        );
    }
}
