<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Maintainerati\Bikeshed\Repository\OneTimeKeyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class OneTimeKeysController extends AbstractController
{
    public function __invoke(OneTimeKeyRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Attendee tried to access a page without authentication');

        return $this->render('@Bikeshed/admin/one-time-keys/index.html.twig', [
            'keys' => $repo->findValid(),
        ]);
    }
}
