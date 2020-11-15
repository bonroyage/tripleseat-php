<?php namespace Tripleseat\Services;

use Tripleseat\Operations;

/**
 * A booking represents block of time for an event or multiple events. Bookings
 * can have multiple events.
 *
 * @url https://support.tripleseat.com/hc/en-us/articles/212528827-Bookings-API
 */
class Booking extends Service
{
    public const PATH = "bookings";

    use Operations\AllPaged;
    use Operations\SearchPaged;
    use Operations\Get;
    use Operations\Create;
    use Operations\Update;
    use Operations\Delete;

    protected function parsePayload(array $data): array
    {
        if (array_key_exists('booking', $data)) {
            return $data;
        }

        return ['booking' => $data];
    }
}