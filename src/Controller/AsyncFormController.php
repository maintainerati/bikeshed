<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use Maintainerati\Bikeshed\DataTransfer\FocusFactory;
use Maintainerati\Bikeshed\Responder\AsyncFormResponder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AsyncFormController extends AbstractController
{
    /** @var AsyncFormResponder */
    private $responder;
    /** @var FocusFactory */
    private $focusFactory;

    public function __construct(AsyncFormResponder $responder, FocusFactory $focusFactory)
    {
        $this->responder = $responder;
        $this->focusFactory = $focusFactory;
    }

    public function __invoke(Request $request, ?string $event = null, ?string $session = null, ?string $space = null, ?string $note = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');

        $focus = $this->focusFactory->createFromRequestAttributes($request);
        if (!$focus->isNote()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Attendee tried to access a page without authentication');
        }

        return $this->responder->getResponse($request, $focus);
    }
}
