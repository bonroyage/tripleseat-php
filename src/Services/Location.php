<?php namespace Tripleseat\Services;

use Tripleseat\Operations;

/**
 * A location represents a venue (restaurant, wedding venue, etc.). Locations
 * can have many areas or rooms. Currently we do not allow creating, updating,
 * or deleting locations or areas via our API.
 *
 * @url https://support.tripleseat.com/hc/en-us/articles/212570457-Locations-API
 */
class Location extends Service
{
    public const PATH = "locations";

    use Operations\All;

    protected function parseFromList(array $value)
    {
        return $value['location'];
    }

}