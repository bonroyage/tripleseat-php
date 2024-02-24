<?php

namespace Tripleseat\Services;

use Tripleseat\Http\Client;

abstract class Service
{
    public function __construct(
        protected Client $client,
    ) {
    }

    protected function path(?string $path = null): string
    {
        return static::PATH . ($path ? '/' . $path : '') . '.json';
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

    protected function payloadToObject($data): array
    {
        if (defined('static::OBJECT_KEY') && is_array($data) && array_key_exists(static::OBJECT_KEY, $data)) {
            return $data[static::OBJECT_KEY];
        }

        return $data;
    }
}
