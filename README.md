# PHP wrapper for Tripleseat API

A simple PHP wrapper around [Tripleseat's API](https://support.tripleseat.com/hc/en-us/sections/200821727-Tripleseat-API).

### Getting started

First, create a new instance of the Tripleseat client and provide the API keys for authentication.

```php
use Tripleseat\Tripleseat;

$tripleseat = new Tripleseat([
    'api_key' => '',
    'secret_key' => '',
    'public_key' => ''
]);
```

#### API keys

You'll need to attain your Tripleseat account's API keys. These can be found by logging in to your Tripleseat account and going to Settings -> Tripleseat API.

There is a public and secret key that is used for the majority of the API calls. These values are configured as the `api_key` and `secret_key` respectively.

A second public key is shown separately and is used for some lead operations. This is configured as `public_key`.

### Services

The following services are supported

| Service  | Documentation           | Schema |
| -------- | ----------------------- | ------ |
| Account  | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/212528547-Accounts-API) | [Schema](http://api.tripleseat.com/v1/account_schema.json) |
| Booking  | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/212528827-Bookings-API) | [Schema](http://api.tripleseat.com/v1/booking_schema.json) |
| Contact  | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/211858578-Contacts-API) | [Schema](http://api.tripleseat.com/v1/contact_schema.json) |
| Event    | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/212171807-Events-API) | [Schema](http://api.tripleseat.com/v1/event_schema.json) |
| Lead     | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/212528787-Leads-API) | [Schema](http://api.tripleseat.com/v1/lead_schema.json) |
| Location | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/212570457-Locations-API) | [Schema](http://api.tripleseat.com/v1/location_schema.json) |
| Site     | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/212912147-Sites-API) | [Schema](http://api.tripleseat.com/v1/site_schema.json) |
| User     | [Tripleseat documentation](https://support.tripleseat.com/hc/en-us/articles/212567567-Users-API) | [Schema](http://api.tripleseat.com/v1/user_schema.json) |

### Calling operations

```php
// Option 1: $tripleseat->[service]->[operation]()

$tripleseat->booking->all();
$tripleseat->user->get(1);

// Option 2: $tripleseat->[operation][Service]()

$tripleseat->allBooking();
$tripleseat->getUser(1);
```

### `all` and `search` operations
When querying one of the `all` or `search` endpoints, the client will return a Generator that you can iterate through. These endpoints are paged and return 50 results per page. The client will check the `total_pages` property in the first response and make sure every page gets loaded. The next page will only get loaded once the iterator gets to that point.

Call [`iterator_to_array` (?)](https://www.php.net/manual/en/function.iterator-to-array.php) to convert the Generator to an array and load all pages immediately.

Additionally, you may provide a `$firstPage` or `$untilPage` on these endpoints to change from which page on and/or until which page the data should be loaded (provided it's less than the total number of pages). 

Note: The `site` and `location` services are not paged and do not feature the `$firstPage` or `$untilPage` arguments.

```php
$bookings = $tripleseat->booking->search([
    'query' => 'Birthday',
    'order' => 'created_by'
]);

foreach($bookings as $booking) {
    // do something with $booking
}

// convert from Generator to array
$bookingsArray = iterator_to_array($bookings);
```

### Other operations

| Service | `get(id)` | `create(payload)` | `update(id, payload)` | `delete(id)` |
| ------- | :---------: | :---------------: | :-------------------: | :----------: |
| Account  | ✅ | ✅ | ✅ | ✅ |
| Booking  | ✅ | ✅ | ✅ | ✅ |
| Contact  | ✅ | ✅ | ✅ | ✅ |
| Event    | ✅ | ✅ | ✅ | ✅ |
| Lead     | ✅ | ✅ [(?)](https://support.tripleseat.com/hc/en-us/articles/205161948-Lead-Form-API-endpoint) | ❌ | ✅ |
| Location | ❌ | ❌ | ❌ | ❌ |
| Site     | ❌ | ❌ | ❌ | ❌ |
| User     | ✅ | ❌ | ❌ | ❌ |

```php
$user = $tripleseat->user->get(1);

$tripleseat->lead->create(
    [
        'first_name' => 'john',
        'last_name' => 'doe',
        'email_address' => 'johndoe@example.com',
        'phone_number' => '123-123-1234',
        'company' => 'Example Inc.',
        'event_description' => 'the event desc',
        'event_date' => '11/19/2020',
        'start_time' => '3pm',
        'end_time' => '5pm',
        'guest_count' => '50',
        'additional_information' => 'some more info',
        'location_id' => '1',
    ],
    // The following properties are all optional
    [ 
        'validate_only' => 'true',
        'simple_error_messages' => 'true',
    ]
);

$leadForms = $tripleseat->lead->forms();
```

### Exceptions
All exceptions thrown by this library implement the `Tripleseat\Exceptions\TripleseatException` interface.

### HTTP Client Compatibilities

You could use any [PSR-18](https://www.php-fig.org/psr/psr-18/) compatible client to use with this library. No additional configurations are required. A list of compatible HTTP clients and client adapters can be found at [php-http.org](http://docs.php-http.org/en/latest/clients.html).


```php
$httpClient = // some class implementing Psr\Http\Client\ClientInterface
$tripleseat = new Tripleseat($auth, $httpClient);
```