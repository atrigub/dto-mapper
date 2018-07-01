<?php

namespace DataMapper\Hydrator\Strategy;

use DataMapper\Hydrator\Exception\UnknownStrategyTypeException;

/**
 * Interface StrategyEnabledInterface
 */
interface StrategyEnabledInterface
{
    /**
     * @param string            $name
     * @param StrategyInterface $strategy
     */
    public function addStrategy(string $name, StrategyInterface $strategy): void;

    /**
     * @throws UnknownStrategyTypeException
     *
     * @param string $name
     *
     * @return StrategyInterface
     */
    public function getStrategy(string $name): StrategyInterface;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasStrategy(string $name): bool;

    /**
     * @param string $name
     */
    public function removeStrategy(string $name): void;
}
