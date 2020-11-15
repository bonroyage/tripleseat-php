<?php namespace Tripleseat\Contracts;

interface Http
{
    public function get(string $path, array $query = []);

    public function post(string $path, $body = null, array $query = []);

    public function put(string $path, $body = null, array $query = []);

    public function delete(string $path, array $query = []);
}