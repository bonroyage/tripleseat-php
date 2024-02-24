<?php

namespace Tripleseat\Services;

use Tripleseat\Operations;

class Account extends Service
{
    public const PATH = 'accounts';
    public const OBJECT_KEY = 'account';

    use Operations\AllPaged;
    use Operations\Create;
    use Operations\Delete;
    use Operations\Get;
    use Operations\Update;
}
