# PHP wrapper for Tripleseat API

A simple PHP wrapper around [Tripleseat's API](https://support.tripleseat.com/hc/en-us/sections/200821727-Tripleseat-API).

Requires at least PHP 8.2

Until v1 there may be backward incompatible changes with every minor version (0.x).

### Getting started

First, create a new instance of the Tripleseat client and provide the API keys for authentication.

```bash
$ composer require bonroyage/tripleseat
```

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

### Sites
> A site represents a group of venues. Sites can have multiple locations.

You will most likely only need to retrieve or alter data for a single site, this is also enforced by Tripleseat. For example, you cannot create a contact that belongs to an account in a different site.

An `InvalidSite` exception will be thrown if the site is not in the list of sites that your API keys give you access to.

```php
// Create a new client for Site with ID 1
$mySite = $tripleseat[1];

// Search accounts in this site
$mySite->account->search([
    'query' => 'tripleseat'
]);

// is the same as
$tripleseat->account->search([
    'query' => 'tripleseat', 
    'site_id' => 1
]);
```

#### What does it do in the background?

When you call an offset on the Tripleseat class, it first checks that the site ID is returned by the sites endpoint and then created a new instance of the Tripleseat class with the `site_id` passed as additional property in the `$auth` array.

Every request made with through this class will have `site_id` added to the query parameters of each request.

```php
$sites = $tripleseat->site->all();

$mySite = new Tripleseat([
    'api_key' => '',
    'secret_key' => '',
    'public_key' => '',
    'site_id' => 1
]);
```

#### What about endpoints that don't use site_id?

Endpoints like `site`, `location`, and `user` don't support the `site_id` parameter. They will always return the same result regardless of what site ID is passed.

### `all` and `search` operations
When querying one of the `all` or `search` endpoints, the client will return a PaginatedResponse. These endpoints are paged and return 50 results per page.

The PaginatedResponse class is iterable (over the results loaded in that page). It also features the following helpers:
- `currentPage(): int` - gets the current page number
- `totalPages(): int` - gets the total number of pages
- `hasMore(): bool` - checks if the page number is below the total number of pages
- `next(): ?PaginatedResponse` - get the next page (if applicable)
- `results(): array` - get an array of this page's results
- `all(): array` - load results from all pages into an array
- `untilPage(int $page): array` - load results from current until given page into an array

Note: The `site` and `location` services are not paged.

```php
$bookings = $tripleseat->booking->search([
    'query' => 'Birthday',
    'order' => 'created_by'
]);

foreach($bookings as $booking) {
    // do something with $booking
}
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

$tripleseat->lead->create([
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
]);

$leadForms = $tripleseat->lead->forms();
```

#### Create and update payloads
Pass in the actual payload and the client will automatically wrap this with the correct key for the type. You can also provide an array with the payload already wrapped, this will not be wrapped again.

```php
// Calling ...
$tripleseat->contact->create([
    'account_id' => '',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email_addresses' => [
        [
            'address' => 'johndoe@example.com'
        ]
    ]
]);

// will send the request as ...

[
    'contact' => [
        'account_id' => '',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email_addresses' => [
            [
                'address' => 'johndoe@example.com'
            ]
        ]
    ]
]
```

### Exceptions
All exceptions thrown by this library implement the `Tripleseat\Exceptions\TripleseatException` interface.

### HTTP Client Compatibilities

You could use any [PSR-18](https://www.php-fig.org/psr/psr-18/) compatible client to use with this library. No additional configurations are required. A list of compatible HTTP clients and client adapters can be found at [php-http.org](http://docs.php-http.org/en/latest/clients.html).


```php
$httpClient = // some class implementing Psr\Http\Client\ClientInterface
$tripleseat = new Tripleseat($auth, $httpClient);
```
