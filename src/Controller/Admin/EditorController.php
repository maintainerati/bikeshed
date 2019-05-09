<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\FormHandler\Handler;
use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Maintainerati\Bikeshed\DataTransfer\NoteData;
use Maintainerati\Bikeshed\Entity\Event;
use Maintainerati\Bikeshed\Entity\Note;
use Maintainerati\Bikeshed\Entity\Session;
use Maintainerati\Bikeshed\Entity\Space;
use Maintainerati\Bikeshed\Form\Handler\EventEditFormHandler;
use Maintainerati\Bikeshed\Form\Handler\NoteEditFormHandler;
use Maintainerati\Bikeshed\Form\Handler\SessionEditFormHandler;
use Maintainerati\Bikeshed\Form\Handler\SpaceEditFormHandler;
use Maintainerati\Bikeshed\Util\GitHubEmoji;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditorController extends AbstractController
{
    /** @var HandlerFactoryInterface */
    private $factory;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(HandlerFactoryInterface $factory, EntityManagerInterface $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function __invoke(GitHubEmoji $github, Request $request, string $type, string $id): Response
    {
        if ($type === 'note') {
            // TODO GET RID OF HACK
            $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');
        } else {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Attendee tried to access a page without authentication');
        }

        /** @var Handler $handler */
        if ($type === 'event') {
            $data = $this->em->getRepository(Event::class)->find($id) ?: new Event();
            $handler = $this->factory->create(EventEditFormHandler::class);
        } elseif ($type === 'session') {
            $data = $this->em->getRepository(Session::class)->find($id) ?: new Session();
            $handler = $this->factory->create(SessionEditFormHandler::class);
        } elseif ($type === 'space') {
            $data = $this->em->getRepository(Space::class)->find($id) ?: new Space();
            $handler = $this->factory->create(SpaceEditFormHandler::class);
        } elseif ($type === 'note') {
            $entity = $this->em->getRepository(Note::class)->find($id) ?: new Note();
            $data = NoteData::createFromEntity($entity);
            $handler = $this->factory->create(NoteEditFormHandler::class);
        } else {
            throw new NotFoundHttpException('Type not found');
        }

        $source = $request->headers->get('referer');
        if ($source && $source !== $request->getUri()) {
            $request->getSession()->set('edit_origin', $source);
        }

        $response = $handler->handle($request, $data);
        if ($response instanceof Response) {
            return $response;
        }

        return $this->render('@Bikeshed/admin/editor/index.html.twig', [
            'form' => $handler->getForm()->createView(),
            'emoji' => $github->getJson(),
        ]);
    }
}
