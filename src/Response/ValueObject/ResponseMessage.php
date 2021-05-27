<?php

namespace PoK\Response\ValueObject;

use PoK\Exception\ServerError\InternalServerErrorException;

class ResponseMessage
{
    private $value;
    
    // SUCCESS MESSAGES
    const ACCEPTED_MESSAGE = 'ACCEPTED';
    const CREATED_MESSAGE = 'CREATED';
    const NO_CONTENT_MESSAGE = 'NO_CONTENT';
    const OK_MESSAGE = 'OK';

    // REDIRECT MESSAGES
    const FOUND_MESSAGE = 'FOUND';
    
    public static function getAvailableCodes(): array
    {
        return [
            // SUCCESS MESSAGES
            self::ACCEPTED_MESSAGE,
            self::CREATED_MESSAGE,
            self::NO_CONTENT_MESSAGE,
            self::OK_MESSAGE,
            // REDIRECT MESSAGES
            self::FOUND_MESSAGE
        ];
    }
    
    public function __construct($value)
    {
        $this->value = $value;
        $this->validateValue();
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function __toString(): string
    {
        return (string) $this->value;
    }
    
    private function validateValue()
    {
        if (!in_array($this->value, self::getAvailableCodes()))
            throw new InternalServerErrorException();
        return $this;
    }

    // SUCCESS FACTORIES
    
    public static function makeAccepted(): ResponseMessage
    {
        return new static(self::ACCEPTED_MESSAGE);
    }
    
    public static function makeCreated(): ResponseMessage
    {
        return new static(self::CREATED_MESSAGE);
    }
    
    public static function makeNoContent(): ResponseMessage
    {
        return new static(self::NO_CONTENT_MESSAGE);
    }

    public static function makeOK(): ResponseMessage
    {
        return new static(self::OK_MESSAGE);
    }

    // REDIRECT FACTORIES

    public static function makeFound(): ResponseMessage
    {
        return new static(self::FOUND_MESSAGE);
    }
}
