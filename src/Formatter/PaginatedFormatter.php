<?php

namespace PoK\Formatter;

use PoK\ValueObject\PaginatedCollection;

class PaginatedFormatter implements FormatterInterface
{
    public function format(PaginatedCollection $collection)
    {
        return [
            'total_item_count' => $collection->getTotalItemsCount(),
            'current_item_count' => $collection->getCurrentItemsCount(),
            'page' => $collection->getPage()->getValue(),
            'per_page' => $collection->getPerPage()->getValue(),
            'total_page_count' => $collection->getTotalPageCount(),
            'items' => $collection->toArray()
        ];
    }
}