<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Delete
{

    public function delete(int $id)
    {
        return $this->client->delete(
            $this->path($id)
        );
    }

}