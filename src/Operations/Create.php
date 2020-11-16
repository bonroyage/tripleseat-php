<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Create
{

    public function create(array $data, array $additionalData = [])
    {
        $data = $this->objectToPayload($data);

        return $this->client->post(
            $this->path(),
            $data
        );
    }

}