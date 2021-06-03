<?php

namespace PoK\Formatter;

use PoK\ValueObject\TypeBoolean;

class BooleanFormatter implements FormatterInterface
{
    public function format(TypeBoolean $value)
    {
        return $value->getValue();
    }
}