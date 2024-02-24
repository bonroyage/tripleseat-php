<?php

namespace Tripleseat\Services;

use Tripleseat\Operations;

/**
 * A contact represents an individual. Contacts always belong to an Account.
 *
 * @url https://support.tripleseat.com/hc/en-us/articles/211858578-Contacts-API
 */
class Contact extends Service
{
    public const PATH = 'contacts';
    public const OBJECT_KEY = 'contact';

    use Operations\AllPaged;
    use Operations\Create;
    use Operations\Delete;
    use Operations\Get;
    use Operations\Update;
}
