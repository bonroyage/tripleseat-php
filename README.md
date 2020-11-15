# PHP wrapper for Tripleseat API

First, create a new instance of the Tripleseat client and provide the API keys for authentication.

```php
use Tripleseat\Tripleseat;

$tripleseat = new Tripleseat([
    'api_key' => '',
    'secret_key' => '',
    'public_key' => ''
]);
```

#### Services

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

#### Calling operations

```php
// Option 1: $tripleseat->[service]->[operation]()

$tripleseat->booking->all();
$tripleseat->user->find(1);

// Option 2: $tripleseat->[operation][Service]()

$tripleseat->allBooking();
$tripleseat->findUser(1);
```

#### `all` and `search` operations
When querying one of the `all` or `search` endpoints, the client will return a Generator that you can iterate through. These endpoints are paged and return 50 results per page. The client will check the `total_pages` property in the first response and make sure every page gets loaded. The next page will only get loaded once the iterator gets to that point.

Call `iterator_to_array` to convert the Generator to an array and load all pages immediately.

Additionally, you may provide a `$firstPage` or `$untilPage` on these endpoints to change from which page on and/or until which page the data should be loaded (provided it's less than the total number of pages). 

Note: The `site` and `location` services are not paged and do not feature the `$firstPage` or `$untilPage` arguments.

```php
$bookings = $tripleseat->searchBooking([
    'query' => 'Birthday',
    'order' => 'created_by'
]);

foreach($bookings as $booking) {
    // do something with $booking
}

// convert from Generator to array
$bookingsArray = iterator_to_array($bookings);
```

#### Other operations

| Service | `find(id)` | `create(payload)` | `update(id, payload)` | `delete(id)` |
| ------- | :---------: | :---------------: | :-------------------: | :----------: |
| Account  | ✅ | ✅ | ✅ | ✅ |
| Booking  | ✅ | ✅ | ✅ | ✅ |
| Contact  | ✅ | ✅ | ✅ | ✅ |
| Event    | ✅ | ✅ | ✅ | ✅ |
| Lead     | ✅ | ⌛︎ (1) | ❌ | ❌ |
| Location | ❌ | ❌ | ❌ | ❌ |
| Site     | ❌ | ❌ | ❌ | ❌ |
| User     | ✅ | ❌ | ❌ | ❌ |

1. The create operation for leads isn't implemented yet.

```php
$user = $tripleseat->findUser(1);
```