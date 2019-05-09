<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use Maintainerati\Bikeshed\Repository\NoteRepository;
use Maintainerati\Bikeshed\Repository\SpaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AsyncNotesController extends AbstractController
{
    private $spaceRepo;
    /** @var NoteRepository */
    private $noteRepo;

    public function __construct(SpaceRepository $spaceRepo)
    {
        $this->spaceRepo = $spaceRepo;
    }

    public function __invoke(Request $request, string $session): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');

        return $this->render('@Bikeshed/async/notes.html.twig', [
            'space' => $this->spaceRepo->find($session),
        ]);
    }
}
