<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Maintainerati\Bikeshed\DataTransfer\NoteData;
use Maintainerati\Bikeshed\Entity\Note;
use Maintainerati\Bikeshed\Form\Handler\NoteEditFormHandler;
use Maintainerati\Bikeshed\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NoteController extends AbstractController
{
    /** @var HandlerFactoryInterface */
    private $factory;

    public function __construct(HandlerFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(NoteRepository $repo, Request $request, string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');

        $data = NoteData::createFromEntity($repo->find($id) ?: new Note());
        $handler = $this->factory->create(NoteEditFormHandler::class);
        $source = $request->headers->get('referer');
        if (!$request->isXmlHttpRequest() && $source && $source !== $request->getUri()) {
            $request->getSession()->set('edit_origin', $source);
        }

        $response = $handler->handle($request, $data);
        if ($response instanceof Response) {
            return $response;
        }

        return $this->render('@Bikeshed/note/note.html.twig', [
            'form' => $handler->getForm()->createView(),
        ]);
    }
}
