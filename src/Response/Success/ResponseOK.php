<?php

namespace PoK\Response\Success;

use PoK\Response\Response;
use PoK\Response\ValueObject\ResponseCode;
use PoK\Response\ValueObject\ResponseMessage;

/**
 * The request has succeeded. The information returned with the response is
 * dependent on the method used in the request, for example:
 *  GET an entity corresponding to the requested resource is sent in the response;
 *  HEAD the entity-header fields corresponding to the requested resource are
 *      sent in the response without any message-body;
 *  POST an entity describing or containing the result of the action;
 *  TRACE an entity containing the request message as received by the end server.
 */
class ResponseOK extends Response
{
    public function __construct() {
        parent::__construct(ResponseMessage::makeOK(), ResponseCode::makeOK());
    }
}
