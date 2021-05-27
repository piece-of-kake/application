<?php

namespace PoK\Response;

use PoK\Formatter\FormatterInterface;
use PoK\Response\ValueObject\ResponseCode;
use PoK\Response\ValueObject\ResponseMessage;
use PoK\Exception\ServerError\InternalServerErrorException;

class Response
{
    /**
    * @var array 
    */ 
    private $assignedResponseData = [];
    
    /**
     *
     * @var FormatterInterface 
     */
    private $responseDataFormatter;
    
    /**
     * @var ResponseCode
     */
    protected $code;
    
    /**
     * @var ResponseMessage
     */
    private $message;
    
    /**
     * @var array
     */
    private $headers = [];
    
    public function __construct(ResponseMessage $message, ResponseCode $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @return ResponseCode
     */
    public function getCode(): ResponseCode
    {
        return $this->code;
    }

    /**
     * @return ResponseMessage
     */
    public function getMessage(): ResponseMessage
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * @param mixed $data
     * @return Response
     */
    public function assignResponseData($data)
    {
        $this->assignedResponseData[] = $data;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getAssignedResponseData(): array
    {
        return $this->assignedResponseData;
    }
    
    /**
     * @param FormatterInterface $formatter
     * @return Response
     */
    public function setResponseDataFormatter(FormatterInterface $formatter)
    {
        $this->responseDataFormatter = $formatter;
        return $this;
    }
    
    public function getContent()
    {
        if (!method_exists($this->responseDataFormatter, 'format'))
            throw new InternalServerErrorException();
        
        return $this->responseDataFormatter->format(...$this->assignedResponseData);
    }
    
    public function isJson()
    {
        $this->headers['Content-Type'] = 'application/json';
        return $this;
    }
}
