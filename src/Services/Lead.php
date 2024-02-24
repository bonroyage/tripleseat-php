<?php

namespace Tripleseat\Services;

use Tripleseat\Exceptions\HttpException;
use Tripleseat\Operations;

/**
 * A lead represents a prospective person or interested party. Leads can be
 * converted into Accounts/Contacts and Events.
 *
 * @url https://support.tripleseat.com/hc/en-us/articles/212528787-Leads-API
 * @url https://support.tripleseat.com/hc/en-us/articles/205161948-Lead-Form-API-endpoint
 */
class Lead extends Service
{
    public const PATH = 'leads';
    public const OBJECT_KEY = 'lead';

    use Operations\AllPaged;
    use Operations\Delete;
    use Operations\Get;

    public function create(array $payload): array
    {
        $payload = $this->objectToPayload($payload);

        $response = $this->client->post(
            'leads/create.js',
            $payload,
            [
                'public_key' => $this->client->publicKey(),
            ]
        );

        if (isset($response['errors'])) {
            throw new HttpException(422, $response['errors']);
        }

        return $response;
    }

    public function forms(): array
    {
        $data = $this->client->get(
            path: 'lead_forms.json',
            query: [
                'public_key' => $this->client->publicKey(),
            ]
        );

        return array_column($data, 'lead_form');
    }
}
