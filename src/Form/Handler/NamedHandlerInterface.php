<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Handler;

use Hostnet\Component\FormHandler\HandlerInterface;
use Symfony\Component\HttpFoundation\Request;

interface NamedHandlerInterface extends HandlerInterface
{
    /**
     * Handle the form based on the request.
     *
     * @param ?string optional name for a 'named form'
     */
    public function handle(Request $request, $data = null, ?string $formName = null);
}
