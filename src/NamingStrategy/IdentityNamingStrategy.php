<?php

namespace DataMapper\NamingStrategy;

/**
 * Class IdentityNamingStrategy
 */
class IdentityNamingStrategy implements NamingStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function hydrate(string $name, $context = null): string
    {
        return $name;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(string $name): string
    {
        return $name;
    }
}
