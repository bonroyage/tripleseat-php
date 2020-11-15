<?php namespace Tripleseat\Operations;

use Tripleseat\Services\Service;

/**
 * @mixin Service
 */
trait Update
{

    public function update(int $id, array $data)
    {
        $data = $this->parsePayload($data);

        return $this->client->put(
            $this->path($id),
            $data
        );
    }

}