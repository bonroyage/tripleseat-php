<?php

namespace Tripleseat\Contracts;

interface Http
{
    public function get(string $path, array $query = []): array;

    public function post(string $path, ?array $body = null, array $query = []): array;

    public function put(string $path, ?array $body = null, array $query = []): array;

    public function delete(string $path, array $query = []): array;
}
