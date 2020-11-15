<?php namespace Tripleseat\Services;

use Tripleseat\Operations;

class Account extends Service
{
    public const PATH = "accounts";

    use Operations\AllPaged;
    use Operations\SearchPaged;
    use Operations\Get;
    use Operations\Create;
    use Operations\Update;
    use Operations\Delete;

    protected function parsePayload(array $data): array
    {
        if (array_key_exists('account', $data)) {
            return $data;
        }

        return ['account' => $data];
    }
}