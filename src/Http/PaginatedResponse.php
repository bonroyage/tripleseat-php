<?php

namespace Tripleseat\Http;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use IteratorAggregate;
use Traversable;
use UnexpectedValueException;

readonly class PaginatedResponse implements ArrayAccess, IteratorAggregate
{
    public function __construct(
        private array $response,
        private string $path,
        private array $query,
        private int $page,
        private Client $httpClient,
    ) {
    }

    public function totalPages(): int
    {
        if (!array_key_exists('total_pages', $this->response)) {
            throw new UnexpectedValueException('Response does not include a \'total_pages\' property.');
        }

        return $this->response['total_pages'];
    }

    public function hasMore(): bool
    {
        return $this->page < $this->totalPages();
    }

    public function currentPage(): int
    {
        return $this->page;
    }

    public function next(): ?PaginatedResponse
    {
        if (!$this->hasMore()) {
            return null;
        }

        $query = $this->query;
        $query['page'] = $this->page + 1;

        return new self(
            response: $this->httpClient->get($this->path, $query),
            path: $this->path,
            query: $this->query,
            page: $this->page + 1,
            httpClient: $this->httpClient,
        );
    }

    public function results(): array
    {
        if (!array_key_exists('results', $this->response)) {
            throw new UnexpectedValueException('Response does not include a \'results\' property.');
        }

        return $this->response['results'];
    }

    public function all(): array
    {
        return $this->untilPage(PHP_INT_MAX);
    }

    public function untilPage(int $page): array
    {
        $data = [];
        $response = $this;

        do {
            $data = array_merge($data, $response->results());
            $response = $response->next();
        } while ($response instanceof PaginatedResponse && $response->page <= $page);

        return $data;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->results());
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->response['results'][$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->response['results'][$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Not allowed');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Not allowed');
    }
}
