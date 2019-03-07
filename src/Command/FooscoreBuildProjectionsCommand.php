<?php

declare(strict_types=1);

namespace Fooscore\Command;

use Doctrine\DBAL\Connection;
use Fooscore\Gaming\Adapters\DomainEventsFinder;
use Fooscore\Gaming\Adapters\MatchDetailsProjector;
use Fooscore\Gaming\Adapters\MatchSymfonyEvent;
use Fooscore\Gaming\Match\DomainEvent;
use Fooscore\Gaming\Match\MatchId;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FooscoreBuildProjectionsCommand extends Command
{
    protected static $defaultName = 'fooscore:build-projections';

    /**
     * @var MatchDetailsProjector
     */
    private $matchDetailsProjector;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $knownDomainEvents;

    public function __construct(Connection $connection, DomainEventsFinder $domainEventsFinder, string $projectionDir)
    {
        $this->matchDetailsProjector = new MatchDetailsProjector($projectionDir);
        $this->connection = $connection;
        $this->knownDomainEvents = $domainEventsFinder->getDomainEventsClassesIndexedByNames();

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Build projections for all matches.')
            ->addArgument('id', InputArgument::OPTIONAL, 'Only for match id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $matchId = $input->getArgument('id');

        $sql = 'SELECT * FROM event_store WHERE aggregate_type = :aggregate_type';
        $params = ['aggregate_type' => 'match'];

        if (is_string($matchId)) {
            new MatchId(Uuid::fromString($matchId));
            $io->note(sprintf('Building projection for match id: %s', $matchId));
            $sql .= ' AND aggregate_id = :aggregate_id';
            $params += ['aggregate_id' => $matchId];
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        $domainEventsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $events = array_map(function (array $domainEventArray): MatchSymfonyEvent {
            foreach ($this->knownDomainEvents as $knownDomainEventName => $knownDomainEventClass) {
                if ($domainEventArray['event_name'] === $knownDomainEventName) {
                    /* @var DomainEvent $knownDomainEventClass */
                    $domainEvent = $knownDomainEventClass::fromEventDataArray(
                        json_decode($domainEventArray['event_data'], true)
                    );

                    return new MatchSymfonyEvent(new MatchId(Uuid::fromString($domainEventArray['aggregate_id'])), $domainEvent);
                }
            }

            throw new \RuntimeException(sprintf('Unknown domain event name : %s', $domainEventArray['event_name']));
        }, $domainEventsArray);

        foreach ($events as $event) {
            $this->matchDetailsProjector->on($event);
        }

        $io->success('Done.');
    }
}
