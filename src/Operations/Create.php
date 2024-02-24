<?php

namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Create
{
    public function create(array $data): array
    {
        $data = $this->objectToPayload($data);

        $response = $this->client->post(
            path: $this->path(),
            body: $data
        );

        return $this->payloadToObject($response);
    }
}
