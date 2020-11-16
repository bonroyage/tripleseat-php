<?php namespace Tripleseat;

use Psr\Http\Client\ClientInterface;
use Tripleseat\Exceptions\InvalidArgumentException;
use Tripleseat\Exceptions\InvalidAuthConfiguration;
use Tripleseat\Exceptions\InvalidService;
use Tripleseat\Exceptions\InvalidSite;
use Tripleseat\Http\Client as HttpClient;
use Tripleseat\Services;

/**
 * @property Services\Account account
 * @property Services\Booking booking
 * @property Services\Contact contact
 * @property Services\Event event
 * @property Services\Lead lead
 * @property Services\Location location
 * @property Services\Site site
 * @property Services\User user
 */
class Tripleseat implements \ArrayAccess
{
    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @var array
     */
    private $services = [];

    /**
     * @var string[]
     */
    private $availableServices = [
        'account' => Services\Account::class,
        'booking' => Services\Booking::class,
        'contact' => Services\Contact::class,
        'event' => Services\Event::class,
        'lead' => Services\Lead::class,
        'location' => Services\Location::class,
        'site' => Services\Site::class,
        'user' => Services\User::class,
    ];

    /**
     * @var array
     */
    private $sites = null;

    /**
     * @var array
     */
    private $auth;

    /**
     * @var ClientInterface|null
     */
    private $httpClient;

    public function __construct(array $auth, ClientInterface $httpClient = null)
    {
        if (!isset($auth['api_key'])) {
            throw new InvalidAuthConfiguration("Missing 'api_key' from auth argument");
        }

        if (!isset($auth['secret_key'])) {
            throw new InvalidAuthConfiguration("Missing 'secret_key' from auth argument");
        }

        if (!isset($auth['public_key'])) {
            throw new InvalidAuthConfiguration("Missing 'public_key' from auth argument");
        }

        $this->auth = $auth;
        $this->httpClient = $httpClient;
        $this->http = new HttpClient($auth, $httpClient);
    }

    /**
     * @param string $name
     * @return Services\Service
     * @throws InvalidService
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->availableServices)) {
            if (!array_key_exists($name, $this->services)) {
                $serviceClass = $this->availableServices[$name];
                $this->services[$name] = new $serviceClass($this->http);
            }

            return $this->services[$name];
        }

        throw new InvalidService($name);
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        // Load and cache the sites
        if (is_null($this->sites)) {
            $this->sites = [];

            foreach ($this->site->all() as $site) {
                $this->sites[$site['id']] = null;
            }
        }

        return array_key_exists($offset, $this->sites);
    }

    /**
     * @param int $offset
     * @return Tripleseat
     * @throws InvalidSite
     */
    public function offsetGet($offset)
    {
        if (isset($this[$offset])) {
            if (is_null($this->sites[$offset])) {
                $this->sites[$offset] = new self(array_merge($this->auth, ['site_id' => $offset]), $this->httpClient);
            }

            return $this->sites[$offset];
        }

        throw new InvalidSite("Site with ID '{$offset}' not found");
    }

    public function offsetSet($offset, $value)
    {
        throw new InvalidArgumentException("Cannot set index");
    }

    public function offsetUnset($offset)
    {
        throw new InvalidArgumentException("Cannot unset index");
    }
}