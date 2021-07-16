<?php

namespace PoK\Exceptions\Internal;

use PoK\Exception\ServerError\InternalServerErrorException;

class FailedHTTPRequestException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL) {
        parent::__construct('FAILED_HTTP_REQUEST', $previous);
    }
}
