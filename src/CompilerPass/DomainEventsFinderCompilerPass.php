<?php

declare(strict_types=1);

namespace Fooscore\CompilerPass;

use Fooscore\Gaming\Infrastructure\DomainEventsFinder;
use Fooscore\Gaming\Infrastructure\SymfonyDomainEventsFinder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DomainEventsFinderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
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
