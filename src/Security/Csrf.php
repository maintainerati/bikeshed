<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Security;

final class Csrf
{
    public const TOKEN_ID = 'authenticate';
    public const PARAMETER = '_csrf_token';
}
