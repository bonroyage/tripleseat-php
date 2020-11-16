<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Create
{

    public function create(array $data)
    {
        $data = $this->objectToPayload($data);

        $response = $this->client->post(
            $this->path(),
            $data
        );

        return $this->payloadToObject($response);
    }

}