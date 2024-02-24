<?php

namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Get
{
    public function get(int $id): array
    {
        $response = $this->client->get(
            path: $this->path($id)
        );

        return $this->payloadToObject($response);
    }
}
