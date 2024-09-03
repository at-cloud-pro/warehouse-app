<?php

declare(strict_types=1);

namespace App\Tests\Behat\Helper;

enum HttpMethod: string
{
    case GET = 'GET';

    case POST = 'POST';

    case PATCH = 'PATCH';

    case PUT = 'PUT';

    case DELETE = 'DELETE';
}
