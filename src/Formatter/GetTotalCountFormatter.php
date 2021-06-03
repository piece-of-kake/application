<?php

namespace PoK\Formatter;

use PoK\ValueObject\TypeInteger;

class GetTotalCountFormatter implements FormatterInterface
{
    public function format(TypeInteger $unreadCount)
    {
        return [
            'total_count' => $unreadCount->getValue()
        ];
    }
}