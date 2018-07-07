<?php

namespace Tests\TestCase;

use DataMapper\Hydrator\HydratorFactory;
use DataMapper\Hydrator\HydratorInterface;
use DataMapper\Hydrator\ObjectHydrator;
use DataMapper\Strategy\ClosureStrategy;
use DataMapper\Strategy\GetterStrategy;
use DataMapper\Strategy\XPathGetterStrategy;
use DataMapper\Type\TypeResolver;

use Tests\DataFixtures\Dto\DeepValueDto;
use Tests\DataFixtures\Model\Outer;
use Tests\DataFixtures\Traits\BaseMappingTrait;

use PHPUnit\Framework\TestCase;

/**
 * Class ChainStrategyTest
 */
class ChainStrategyTest extends TestCase
{
    use BaseMappingTrait;

    /**
     */
    public function testChainStrategyHydration()
    {
        $outer = new Outer();
        $pathTOValue = 'inner.deep.searchValue';
        $searchString = 'Returned from closure';

        $hydrator = $this->createChainHydrator(
            $outer,
            DeepValueDto::class,
            [
                [
                    'found',
                    [
                        new ObjectHydrator(),
                        $pathTOValue,
                    ],
                    XPathGetterStrategy::class,
                ],
                [
                    'test',
                    [
                        function (Outer $outer) use ($searchString): string {
                            return $outer->getInner()->getDeep()->getDeepValue() . $searchString;
                        },
                    ],
                    ClosureStrategy::class,
                ],
                [
                    'destinationGetterTarget',
                    [
                        'getTestGetter',
                    ],
                    GetterStrategy::class,
                ]
            ]
        );

        $dto = $hydrator->hydrate($outer, DeepValueDto::class);
        $this->assertEquals($dto->getFound(), $outer->getInner()->getDeep()->getDeepValue());
        $this->assertContains($searchString, $dto->getTest());
        $this->assertEquals($dto->getDestinationGetterTarget(), $outer->getTestGetter());
        $this->assertEquals($dto->getCopiedByName(), $outer->getCopiedByName());
    }

    /**
     * @param object $source
     * @param string $destinationClass
     * @param array  $mapping
     *
     * @return HydratorInterface
     */
    private function createChainHydrator(object $source, string $destinationClass, array $mapping): HydratorInterface
    {
        $mappingRegistry = $this->createMappingRegistry();
        $hydrationRegistry = $this->createHydrationRegistry();
        $mappingRegistry
            ->getDestinationRegistry()
            ->registerDestinationClass($destinationClass);

        $typeStrategyType = TypeResolver::getStrategyType($source, $destinationClass);
        foreach ($mapping as [$destinationProperty, $args, $strategy]) {
            $mappingRegistry
                ->getStrategyRegistry()
                ->registerPropertyStrategy($typeStrategyType, $destinationProperty, $strategy, $args);
        }

        return (new HydratorFactory($hydrationRegistry, $mappingRegistry))->createHydrator($source, $destinationClass);
    }
}