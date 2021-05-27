<?php

namespace PoK\Domain\Factory;

use PoK\ValueObject\Collection;

interface ReconstitutesCollection extends Reconstitutes
{
    public function reconstitute(Collection $data);
}
