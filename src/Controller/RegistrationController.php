<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Maintainerati\Bikeshed\Entity\Attendee;
use Maintainerati\Bikeshed\Form\Handler\RegistrationFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends AbstractController
{
    /** @var HandlerFactoryInterface */
    private $factory;

    public function __construct(HandlerFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request): Response
    {
        $user = new Attendee();
        $handler = $this->factory->create(RegistrationFormHandler::class);
        $response = $handler->handle($request, $user);
        if ($response instanceof Response) {
            return $response;
        }

        return $this->render('@Bikeshed/registration/register.html.twig', [
            'form' => $handler->getForm()->createView(),
        ]);
    }
}
