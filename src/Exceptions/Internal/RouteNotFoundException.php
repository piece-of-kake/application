<?php

namespace PoK\Exceptions\Internal;

class RouteNotFoundException extends \Exception
{
    public function __construct(\Throwable $previous = NULL)
    {
        parent::__construct('ROUTE_NOT_FOUND', 404, $previous);
    }
}
