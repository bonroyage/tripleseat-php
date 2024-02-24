<?php

namespace Tripleseat\Exceptions;

use Exception;

class HttpException extends Exception implements TripleseatException
{
    public $httpStatus = 0;
    public $httpMessage = null;
    public $httpBody = null;

    public function __construct($httpStatus, $httpBody, $previous = null)
    {
        $this->httpBody = $httpBody;
        $this->httpMessage = $this->getMessageFromHttpBody();
        $this->httpStatus = $httpStatus;

        parent::__construct('Error', $this->httpStatus, $previous);
    }

    public function getMessageFromHttpBody()
    {
        if (is_array($this->httpBody) && array_key_exists('text', $this->httpBody)) {
            return $this->httpBody['text'];
        } elseif (is_array($this->httpBody)) {
            return json_encode($this->httpBody);
        }

        return $this->httpBody;
    }

    public function __toString(): string
    {
        $base = 'Tripleseat HttpException: Http Status: ' . $this->httpStatus;

        if ($this->httpMessage) {
            return $base . ' - Message: ' . $this->httpMessage;
        }

        return $base;
    }
}
