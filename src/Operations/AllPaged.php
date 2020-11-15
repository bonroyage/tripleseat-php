<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait AllPaged
{

    public function all(int $fromPage = 1, int $untilPage = PHP_INT_MAX): \Generator
    {
        return $this->client->getPaged($this->path(), [], $fromPage, $untilPage);
    }

}