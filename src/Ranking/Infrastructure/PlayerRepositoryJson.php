<?php

declare(strict_types=1);

namespace Fooscore\Ranking\Infrastructure;

use Fooscore\Ranking\Player;
use Fooscore\Ranking\PlayerRepository;

class PlayerRepositoryJson implements PlayerRepository
{
    /**
     * @var string
     */
    private $scoreFile;

    public function __construct(string $scoreFile)
    {
        $this->scoreFile = $scoreFile;
    }

    public function get(string $id): Player
    {
        $data = $this->read();
        if (!isset($data[$id])) {
            $data[$id] = 0;
        }

        return new Player($id, $data[$id]);
    }

    public function save(Player $player): bool
    {
        $data = $this->read();
        $data[$player->id()] = $player->score();

        return $this->write($data);
    }

    private function read(): array
    {
        $json = @\file_get_contents($this->scoreFile);
        if (false === $json) {
            throw new \RuntimeException(sprintf('The file "%s" cannot be read.', $this->scoreFile));
        }

        return \json_decode($json, true);
    }

    private function write(array $data): bool
    {
        return false !== \file_put_contents($this->scoreFile, \json_encode($data)) ? true : false;
    }
}
