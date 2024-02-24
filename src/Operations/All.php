<?php

namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait All
{
    public function all(array $query = []): array
    {
        $data = $this->client->get(
            path: $this->path(),
            query: $query
        );

        return array_map($this->payloadToObject(...), $data);
    }
}
