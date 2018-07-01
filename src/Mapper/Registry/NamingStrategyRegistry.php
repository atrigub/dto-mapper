<?php

namespace DataMapper\Mapper\Registry;

use DataMapper\RegistryContainer;
use DataMapper\Hydrator\NamingStrategy\NamingStrategyInterface;
use DataMapper\TypeResolver;

/**
 * Class DestinationRegistry
 */
class NamingStrategyRegistry extends RegistryContainer implements NamingStrategyRegistryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getRegisteredNamingStrategyFor($destination): ?NamingStrategyInterface
    {
        $destination = TypeResolver::resolveStrategyType($destination);

        return $this->offsetExists($destination) ?
            $this->offsetGet($destination): null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasRegisteredNamingStrategyFor(string $destination): bool
    {
        return $this->offsetGet($destination);
    }

    /**
     * {@inheritDoc}
     */
    public function registerNamingStrategy(string $destination, NamingStrategyInterface $strategy): void
    {
        $this->offsetSet($destination, $strategy);
    }
}
