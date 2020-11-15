<?php namespace Tripleseat;

use Generator;
use Psr\Http\Client\ClientInterface;
use Tripleseat\Exceptions\InvalidAuthConfiguration;
use Tripleseat\Exceptions\InvalidService;
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
 *
 * @method Generator allAccount(int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method Generator searchAccount(array $parameters, int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method getAccount(int $id)
 * @method createAccount(array $account)
 * @method updateAccount(int $id, array $account)
 * @method deleteAccount(int $id)
 *
 * @method Generator allBooking(int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method Generator searchBooking(array $parameters, int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method getBooking(int $id)
 * @method createBooking(array $booking)
 * @method updateBooking(int $id, array $booking)
 * @method deleteBooking(int $id)
 *
 * @method Generator allContact(int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method Generator searchContact(array $parameters, int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method getContact(int $id)
 * @method createContact(array $contact)
 * @method updateContact(int $id, array $contact)
 * @method deleteContact(int $id)
 *
 * @method Generator allEvent(int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method Generator searchEvent(array $parameters, int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method getEvent(int $id)
 * @method createEvent(array $event)
 * @method updateEvent(int $id, array $event)
 * @method deleteEvent(int $id)
 *
 * @method Generator allLead(int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method Generator searchLead(array $parameters, int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method getLead(int $id)
 * @method createLead(array $lead, array $additionalData = [])
 * @method Generator formsLead()
 *
 * @method Generator allLocation()
 *
 * @method Generator allSite()
 *
 * @method Generator allUser(int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method Generator searchUser(array $parameters, int $fromPage = 1, int $untilPage = PHP_INT_MAX)
 * @method getUser(int $id)
 */
class Tripleseat
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
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws InvalidService
     */
    public function __call($name, $arguments)
    {
        if ([$service, $method] = $this->extractServiceAndMethod($name)) {
            $service = $this->__get($service);

            if (!empty($method)) {
                return call_user_func_array([$service, $method], $arguments);
            }
        }

        throw new InvalidService($name);
    }

    /**
     * Parses a string to check if the last part matches any of the defined
     * services and returns an array with the service and the string in front
     * of the service as the method name.
     *
     * @param string $haystack
     * @return array|null
     */
    private function extractServiceAndMethod(string $haystack)
    {
        $haystack = strtolower($haystack);
        $needles = array_keys($this->availableServices);

        foreach ($needles as $needle) {
            if ($needle !== '' && substr_compare($haystack, (string)$needle, -strlen($needle), null, true) === 0) {
                return [strtolower($needle), strstr($haystack, (string)$needle, true)];
            }
        }

        return null;
    }
}