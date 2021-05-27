<?php

namespace PoK\Response\ValueObject;

use PoK\Exception\ServerError\InternalServerErrorException;

class ResponseCode
{
    private $value;
    
    // SUCCESS CODES
    const ACCEPTED_CODE = 202;
    const CREATED_CODE = 201;
    const NO_CONTENT_CODE = 204;
    const OK_CODE = 200;

    // REDIRECT CODES
    const FOUND_CODE = 302;
    
    public static function getAvailableCodes(): array
    {
        return [
            // SUCCESS CODES
            self::ACCEPTED_CODE,
            self::CREATED_CODE,
            self::NO_CONTENT_CODE,
            self::OK_CODE,
            // REDIRECT CODES,
            self::FOUND_CODE
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
    
    public static function makeAccepted(): ResponseCode
    {
        return new static(self::ACCEPTED_CODE);
    }
    
    public static function makeCreated(): ResponseCode
    {
        return new static(self::CREATED_CODE);
    }
    
    public static function makeNoContent(): ResponseCode
    {
        return new static(self::NO_CONTENT_CODE);
    }

    public static function makeOK(): ResponseCode
    {
        return new static(self::OK_CODE);
    }

    // REDIRECT FACTORIES

    public static function makeFound(): ResponseCode
    {
        return new static(self::FOUND_CODE);
    }
}
