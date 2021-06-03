<?php

namespace PoK\Formatter;

use PoK\ValueObject\TypeBoolean;

class AvailabilityFormatter implements FormatterInterface
{
    public function format(TypeBoolean $availability)
    {
        return [
            'available' => $availability->getValue()
        ];
    }
}