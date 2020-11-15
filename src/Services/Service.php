<?php namespace Tripleseat\Services;

use Tripleseat\Http\Client;

abstract class Service
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function path(string $path = null)
    {
        return static::PATH . ($path ? "/" . $path : "") . ".json";
    }

    protected function parsePayload(array $data): array
    {
        return $data;
    }

    protected function parseFromList(array $data)
    {
        return $data;
    }

}