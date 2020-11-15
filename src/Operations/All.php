<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait All
{

    public function all(): \Generator
    {
        $data = $this->client->get($this->path());

        foreach ($data as $result) {
            yield $this->parseFromList($result);
        }
    }

}