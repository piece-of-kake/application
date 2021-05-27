<?php

namespace PoK\Response\Redirect;

use PoK\Response\Response;
use PoK\Response\ResponseIsRedirect;
use PoK\Response\ValueObject\ResponseCode;
use PoK\Response\ValueObject\ResponseMessage;

/**
 * The requested resource resides temporarily under a different URI. Since the
 * redirection might be altered on occasion, the client SHOULD continue to use
 * the Request-URI for future requests. This response is only cacheable if
 * indicated by a Cache-Control or Expires header field.
 *
 * The temporary URI SHOULD be given by the Location field in the response.
 * Unless the request method was HEAD, the entity of the response SHOULD
 * contain a short hypertext note with a hyperlink to the new URI(s).
 * If the 302 status code is received in response to a request other than GET
 * or HEAD, the user agent MUST NOT automatically redirect the request unless
 * it can be confirmed by the user, since this might change the conditions
 * under which the request was issued.
 */
class ResponseFound extends Response implements ResponseIsRedirect
{
    private $location;

    public function __construct(string $location) {
        parent::__construct(ResponseMessage::makeFound(), ResponseCode::makeFound());
        $this->location = $location;
        $this->setHeader('Location', $location); // Should be a cleaner way
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}