<?php

namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Delete
{
    public function delete(int $id): array
    {
        $response = $this->client->delete(
            path: $this->path($id)
        );

        return $this->payloadToObject($response);
    }
}
