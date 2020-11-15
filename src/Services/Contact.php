<?php namespace Tripleseat\Services;

use Tripleseat\Operations;

/**
 * A contact represents an individual. Contacts always belong to an Account.
 *
 * @url https://support.tripleseat.com/hc/en-us/articles/211858578-Contacts-API
 */
class Contact extends Service
{
    public const PATH = "contacts";

    use Operations\AllPaged;
    use Operations\SearchPaged;
    use Operations\Get;
    use Operations\Create;
    use Operations\Update;
    use Operations\Delete;

    protected function parsePayload(array $data): array
    {
        if (array_key_exists('contact', $data)) {
            return $data;
        }

        return ['contact' => $data];
    }
}