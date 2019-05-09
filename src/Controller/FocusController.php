<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Maintainerati\Bikeshed\DataTransfer\FocusFactory;
use Maintainerati\Bikeshed\Form\Handler\FocusFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FocusController extends AbstractController
{
    /** @var HandlerFactoryInterface */
    private $factory;

    public function __construct(HandlerFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request, FocusFactory $focusFactory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');

        $handler = $this->factory->create(FocusFormHandler::class);
        $response = $handler->handle($request, $focusFactory->createFromRequestForm($request));
        if ($response instanceof Response) {
            return $response;
        }

        return $this->render('@Bikeshed/focus/index.html.twig', [
            'form' => $handler->getForm()->createView(),
        ]);
    }
}
