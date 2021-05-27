<?php

namespace PoK\Domain\Caching;

class Key
{
    const KEY_PARTS_DELIMITER = '|';
    const DECAY_PERIOD_ONDE_DAY = 86400;
    
    private $suffixes = [];
    private $decayPeriod = self::DECAY_PERIOD_ONDE_DAY;
    
    public function __construct(...$suffixes)
    {
        $this->suffixes = $suffixes;
    }
    
    protected function setDecayPeriod(int $period)
    {
        $this->decayPeriod = $period;
        return $this;
    }
    
    public function getDecayPeriod(): int
    {
        return $this->decayPeriod;
    }
    
    public function assignFixedPart($fixedPart)
    {
        $this->suffixes[] = $fixedPart;
        return $this;
    }
    
    public function __toString()
    {
        $keyName = __CLASS__;
        if (!empty($this->suffixes)) $keyName .= self::KEY_PARTS_DELIMITER.implode(self::KEY_PARTS_DELIMITER, $this->suffixes);
        
        return sha1($keyName);
    }
}
