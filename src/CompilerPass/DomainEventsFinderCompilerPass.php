<?php

declare(strict_types=1);

namespace Fooscore\CompilerPass;

use Fooscore\Gaming\Adapters\DomainEventsFinder;
use Fooscore\Gaming\Adapters\SymfonyDomainEventsFinder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DomainEventsFinderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $domainEventClasses = array_keys(
            $container->findTaggedServiceIds('fooscore_gaming_match.domain_event')
        );

        $container
            ->register(DomainEventsFinder::class)
            ->setClass(SymfonyDomainEventsFinder::class)
            ->addArgument($domainEventClasses);
    }
}
