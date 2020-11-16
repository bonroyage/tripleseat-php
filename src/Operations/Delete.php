<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Delete
{

    public function delete(int $id)
    {
        $response = $this->client->delete(
            $this->path($id)
        );

        return $this->payloadToObject($response);
    }

}