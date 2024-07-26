<?php

namespace PoK\Request;

use PoK\Validator\Request\ParameterManipulationInterface;
use PoK\ValueObject\Collection;

class ProcessorRequest implements ParameterManipulationInterface
{
    /**
     * @var Collection
     */
    private $parameters;

    /**
     * @var Collection
     */
    private $uploadedFiles;

    /**
     * @var Collection
     */
    private $headers;

    public function __construct(array $parameters = [], array $uploadedFiles = [], array $headers = []) {
        $this->parameters = new Collection($parameters);
        $this->uploadedFiles = new Collection($uploadedFiles);
        $this->headers = new Collection($headers);
    }

    public function getGlobalIpAddress()
    {
        // https://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public function getGlobalReferrer()
    {
        return array_key_exists('HTTP_REFERER', $_SERVER)
            ? $_SERVER['HTTP_REFERER']
            : '';
    }

    public function getGlobalUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function setParameter($name, $value): ProcessorRequest
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function getParameter($name)
    {
        return $this->parameters->has($name)
            ? $this->parameters[$name]
            : null;
    }

    public function hasParameter($name): bool
    {
        return $this->parameters->has($name);
    }

    public function setUploadedFile($name, $file): ProcessorRequest
    {
        $this->uploadedFiles[$name] = $file;
        return $this;
    }

    public function getUploadedFile($name)
    {
        return $this->uploadedFiles[$name];
    }

    public function hasUploadedFile($name): bool
    {
        return $this->uploadedFiles->has($name);
    }

    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    public function hasHeader($name)
    {
        return $this->headers->has($name);
    }
}
