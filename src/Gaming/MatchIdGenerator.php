<?php

namespace Fooscore\Gaming;

interface MatchIdGenerator
{
    public function generate(): MatchId;
}
