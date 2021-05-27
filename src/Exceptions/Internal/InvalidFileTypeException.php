<?php

namespace PoK\Exceptions\Internal;

use PoK\Exception\ServerError\InternalServerErrorException;

class InvalidFileTypeException extends InternalServerErrorException
{
    public function __construct(\Throwable $previous = NULL) {
        parent::__construct('INVALID_FILE_TYPE', $previous);
    }
}
