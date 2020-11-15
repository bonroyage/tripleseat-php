<?php namespace Tripleseat\Services;

use Tripleseat\Operations;

/**
 * A lead represents a prospective person or interested party. Leads can be
 * converted into Accounts/Contacts and Events.
 *
 * @url https://support.tripleseat.com/hc/en-us/articles/212528787-Leads-API
 */
class Lead extends Service
{
    public const PATH = "leads";

    use Operations\AllPaged;
    use Operations\SearchPaged;
    use Operations\Get;
    use Operations\Delete;
}