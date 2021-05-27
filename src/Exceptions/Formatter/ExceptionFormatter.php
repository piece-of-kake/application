<?php

namespace PoK\Exceptions\Formatter;

use PoK\Exception\HasDataInterface;

class ExceptionFormatter
{
    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function toJSON($trace = false): string
    {
        $code = $this->exception->getCode() > 0
            ? $this->exception->getCode()
            : 500;

        $payload = [
            'message' => $this->exception->getMessage(),
            'code' => $code
        ];

        if ($this->exception instanceof HasDataInterface) {
            $payload['data'] = $this->exception->getData();
        }

        if ($trace) {
            // Trace data
            $payload['file'] = $this->exception->getFile();
            $payload['line'] = $this->exception->getLine();
            $payload['trace'] = $this->exception->getTrace();
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    public function toDump(): array
    {
        $payload = [
            'message' => $this->exception->getMessage(),
            'code' => $this->exception->getCode() > 0
                ? $this->exception->getCode()
                : 500,
            'file' => $this->exception->getFile(),
            'line' => $this->exception->getLine(),
            'trace' => json_encode($this->exception->getTrace(), JSON_UNESCAPED_UNICODE)
        ];

        if ($this->exception instanceof HasDataInterface) $payload['data'] = print_r($this->exception->getData(), true);

        return $payload;
    }
}