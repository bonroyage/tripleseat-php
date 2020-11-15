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

    protected function objectToPayload(array $data): array
    {
        if (defined('static::OBJECT_KEY')) {
            if (array_key_exists(static::OBJECT_KEY, $data)) {
                return $data;
            }

            return [static::OBJECT_KEY => $data];
        }

        return $data;
    }

    protected function payloadToObject(array $data)
    {
        if (defined('static::OBJECT_KEY') && array_key_exists(static::OBJECT_KEY, $data)) {
            return $data[static::OBJECT_KEY];
        }

        return $data;
    }

}