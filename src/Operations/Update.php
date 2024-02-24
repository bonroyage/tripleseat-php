<?php

namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Update
{
    public function update(int $id, array $data): array
    {
        $data = $this->objectToPayload($data);

        $response = $this->client->put(
            path: $this->path($id),
            body: $data
        );

        return $this->payloadToObject($response);
    }
}
