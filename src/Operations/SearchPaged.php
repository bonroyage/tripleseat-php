<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait SearchPaged
{

    public function search(array $parameters, int $fromPage = 1, int $untilPage = PHP_INT_MAX): \Generator
    {
        return $this->client->getPaged($this->path('search'), $parameters, $fromPage, $untilPage);
    }

}