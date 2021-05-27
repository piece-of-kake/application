<?php

namespace PoK\Exceptions\Internal;

use PoK\Exception\ServerError\InternalServerErrorException;

class NotAFileException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL) {
        parent::__construct('NOT_A_FILE', $previous);
    }
}
