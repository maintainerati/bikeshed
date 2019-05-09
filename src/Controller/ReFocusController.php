<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use Maintainerati\Bikeshed\DataTransfer\FocusData;
use Maintainerati\Bikeshed\DataTransfer\FocusFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReFocusController extends AbstractController
{
    /** @var FocusFactory */
    private $focusFactory;

    public function __construct(FocusFactory $focusFactory)
    {
        $this->focusFactory = $focusFactory;
    }

    public function __invoke(Request $request, string $event, string $session, string $space): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');

        $focus = $this->focusFactory->createFromSession(false)
            ->setEventId($event)
            ->setSessionId($session)
            ->setSpaceId($space)
        ;
        $request->getSession()->set(FocusData::KEY, $focus);

        if ($focus->isFocused()) {
            return $this->redirectToRoute('bikeshed_space');
        }

        return $this->redirectToRoute('bikeshed_focus');
    }
}
