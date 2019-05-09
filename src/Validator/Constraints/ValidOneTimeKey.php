<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ValidOneTimeKey extends Constraint
{
    public $message = 'Provided key "{{ key }}" has expired or is invalid.';
}
