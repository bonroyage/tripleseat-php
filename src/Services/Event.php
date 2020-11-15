<?php namespace Tripleseat\Services;

use Tripleseat\Operations;

/**
 * An event represents just that, and event at a given time and location.
 * Events always have at least one booking, account, contact, location, and
 * room. An event's booking can contain several events spanning multiple days.
 * An event's payments, billing item totals, line items, and category totals
 * can also be included by adding the parameter '&show_financial=true'
 *
 * @url https://support.tripleseat.com/hc/en-us/articles/212171807-Events-API
 */
class Event extends Service
{
    public const PATH = "events";

    use Operations\AllPaged;
    use Operations\SearchPaged;
    use Operations\Get;
    use Operations\Create;
    use Operations\Update;
    use Operations\Delete;

    protected function parsePayload(array $data): array
    {
        if (array_key_exists('event', $data)) {
            return $data;
        }

        return ['event' => $data];
    }
}