<?php

namespace Tripleseat;

use ArrayAccess;
use Psr\Http\Client\ClientInterface;
use Tripleseat\Exceptions\InvalidArgumentException;
use Tripleseat\Exceptions\InvalidAuthConfiguration;
use Tripleseat\Exceptions\InvalidService;
use Tripleseat\Exceptions\InvalidSite;
use Tripleseat\Http\Client as HttpClient;
use Tripleseat\Services\Service;

/**
 * @property Services\Account $account
 * @property Services\Booking $booking
 * @property Services\Contact $contact
 * @property Services\Event $event
 * @property Services\Lead $lead
 * @property Services\Location $location
 * @property Services\Site $site
 * @property Services\User $user
 */
class Tripleseat implements ArrayAccess
{
    protected HttpClient $http;

    protected array $services = [];

    protected array $availableServices = [
        'account' => Services\Account::class,
        'booking' => Services\Booking::class,
        'contact' => Services\Contact::class,
        'event' => Services\Event::class,
        'lead' => Services\Lead::class,
        'location' => Services\Location::class,
        'site' => Services\Site::class,
        'user' => Services\User::class,
    ];

    protected array $sites;

    public function __construct(
        protected array $auth,
        protected ?ClientInterface $httpClient = null,
    ) {
        if (!isset($auth['api_key'])) {
            throw new InvalidAuthConfiguration("Missing 'api_key' from auth argument");
        }

        if (!isset($auth['secret_key'])) {
            throw new InvalidAuthConfiguration("Missing 'secret_key' from auth argument");
        }

        if (!isset($auth['public_key'])) {
            throw new InvalidAuthConfiguration("Missing 'public_key' from auth argument");
        }

        $this->http = $this->newHttpClient();
    }

    protected function newHttpClient(): HttpClient
    {
        return new HttpClient(
            auth: $this->auth,
            httpClient: $this->httpClient
        );
    }

    public function __get(string $name): Service
    {
        if (!isset($this->availableServices[$name])) {
            throw new InvalidService($name);
        }

        return $this->services[$name] ??= new $this->availableServices[$name](
            client: $this->http,
        );
    }

    public function offsetExists($offset): bool
    {
        // Load and cache the sites
        if (!isset($this->sites)) {
            $this->sites = [];

            foreach ($this->site->all() as $site) {
                $this->sites[$site['id']] = null;
            }
        }

        return array_key_exists($offset, $this->sites);
    }

    public function offsetGet($offset): Tripleseat
    {
        if (!isset($this[$offset])) {
            throw new InvalidSite("Site with ID '{$offset}' not found");
        }

        return $this->sites[$offset] ??= new self(
            auth: [
                ...$this->auth,
                'site_id' => $offset,
            ],
            httpClient: $this->httpClient,
        );
    }

    public function offsetSet($offset, $value): void
    {
        throw new InvalidArgumentException('Cannot set index');
    }

    public function offsetUnset($offset): void
    {
        throw new InvalidArgumentException('Cannot unset index');
    }
}
