<?php

namespace Tripleseat\Operations;

use Tripleseat\Http\PaginatedResponse;
use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait AllPaged
{
    public function all(int $page = 1): PaginatedResponse
    {
        $response = $this->client->get(
            path: $this->path(),
            query: [
                'page' => $page,
            ]
        );

        return new PaginatedResponse(
            response: $response,
            path: $this->path(),
            query: [],
            page: $page,
            httpClient: $this->client,
        );
    }

    public function search(?array $query = [], int $page = 1): PaginatedResponse
    {
        $response = $this->client->get(
            path: $this->path('search'),
            query: [
                ...$query,
                'page' => $page,
            ]
        );

        return new PaginatedResponse(
            response: $response,
            path: $this->path('search'),
            query: $query,
            page: $page,
            httpClient: $this->client,
        );
    }
}
