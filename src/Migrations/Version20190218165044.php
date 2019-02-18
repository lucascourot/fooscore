<?php

declare(strict_types=1);

namespace FooscoreMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190218165044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Init event store';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE SCHEMA IF NOT EXISTS public;
SQL
        );

        $this->addSql(<<<SQL
CREATE TABLE public.event_store
(
    event_id UUID PRIMARY KEY NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    event_data JSONB NOT NULL,
    aggregate_id UUID NOT NULL,
    aggregate_type VARCHAR(255) NOT NULL,
    aggregate_version INT,
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL
);
SQL
        );

        $this->addSql(<<<SQL
CREATE UNIQUE INDEX event_store_event_id_uindex ON public.event_store (event_id);
SQL
        );

        $this->addSql(<<<SQL
CREATE UNIQUE INDEX event_store_aggregate_id_aggregate_type_aggregate_version_uindex ON public.event_store (aggregate_id, aggregate_type, aggregate_version);
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
DROP TABLE IF EXISTS public.event_store;
SQL
        );
    }
}
