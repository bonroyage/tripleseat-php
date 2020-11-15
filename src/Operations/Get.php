<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Get
{

    public function get(int $id)
    {
        return $this->client->get(
            $this->path($id)
        );
    }

}