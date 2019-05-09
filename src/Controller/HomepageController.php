<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use Maintainerati\Bikeshed\Responder\HomepageResponder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{
    public function __invoke(Request $request, HomepageResponder $responder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');

        return $responder->getDefault();
    }
}
