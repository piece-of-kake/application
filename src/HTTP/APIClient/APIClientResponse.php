<?php

namespace PoK\HTTP\APIClient;

use PoK\ValueObject\Collection;

class APIClientResponse
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var array
     */
    private $data;

    public function __construct(int $code, array $data = null)
    {
        $this->code = $code;
        $this->data = $data;
    }

    public function isFailure(): bool
    {
        return $this->code >= 400;
    }

    public function isReason(string $reason): bool
    {
        return array_key_exists('message', $this->data)
            ? $this->data['message'] === $reason
            : false;
    }

    public function isRedirection(): bool
    {
        return $this->code >= 300 && $this->code < 400;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function hasParameters(...$keys): bool
    {
        if (!is_array($this->data)) return false;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->data)) return false;
        }
        return true;
    }

    public function getMissing(...$keys): Collection
    {
        $missing = new Collection([]);
        if (!is_array($this->data)) return $missing;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->data)) $missing[] = $key;
        }
        return $missing;
    }

    public function getParameter($key)
    {
        return $this->data[$key];
    }
}