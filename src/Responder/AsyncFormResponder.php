<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Responder;

use Doctrine\Common\Persistence\ObjectRepository;
use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Maintainerati\Bikeshed\DataTransfer\FocusData;
use Maintainerati\Bikeshed\DataTransfer\NoteData;
use Maintainerati\Bikeshed\Entity\Event;
use Maintainerati\Bikeshed\Entity\Note;
use Maintainerati\Bikeshed\Entity\Session;
use Maintainerati\Bikeshed\Entity\Space;
use Maintainerati\Bikeshed\Form\Handler\EventEditFormHandler;
use Maintainerati\Bikeshed\Form\Handler\NoteEditFormHandler;
use Maintainerati\Bikeshed\Form\Handler\SessionEditFormHandler;
use Maintainerati\Bikeshed\Form\Handler\SpaceEditFormHandler;
use Maintainerati\Bikeshed\Repository\EventRepository;
use Maintainerati\Bikeshed\Repository\NoteRepository;
use Maintainerati\Bikeshed\Repository\SessionRepository;
use Maintainerati\Bikeshed\Repository\SpaceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class AsyncFormResponder
{
    /** @var HandlerFactoryInterface */
    private $factory;
    /** @var EventRepository */
    private $eventRepo;
    /** @var SessionRepository */
    private $sessionRepo;
    /** @var SpaceRepository */
    private $spaceRepo;
    /** @var NoteRepository */
    private $noteRepo;
    /** @var Environment */
    private $twig;

    public function __construct(
        HandlerFactoryInterface $factory,
        EventRepository $eventRepo,
        SessionRepository $sessionRepo,
        SpaceRepository $spaceRepo,
        NoteRepository $noteRepo,
        Environment $twig
    ) {
        $this->factory = $factory;
        $this->eventRepo = $eventRepo;
        $this->sessionRepo = $sessionRepo;
        $this->spaceRepo = $spaceRepo;
        $this->noteRepo = $noteRepo;
        $this->twig = $twig;
    }

    public function getResponse(Request $request, FocusData $focus): Response
    {
        [$className, $repo] = $this->getRunConfig($focus);
        $handler = $this->factory->create($className);
        $data = $this->getData($repo, $focus);
        $response = $handler->handle($request, $data, $focus->getFormName());
        if ($response instanceof Response) {
            return $response;
        }
        $content = $this->twig->render('@Bikeshed/async/form.html.twig', [
            'focus' => $focus,
            'form' => $handler->getForm()->createView(),
            'type' => $focus->getType(),
            'show_labels' => false,
        ]);

        return new Response($content);
    }

    private function getRunConfig(FocusData $focus): array
    {
        if ($focus->isEvent()) {
            $className = EventEditFormHandler::class;
            $repo = $this->eventRepo;
        } elseif ($focus->isSession()) {
            $className = SessionEditFormHandler::class;
            $repo = $this->sessionRepo;
        } elseif ($focus->isSpace()) {
            $className = SpaceEditFormHandler::class;
            $repo = $this->spaceRepo;
        } elseif ($focus->isNote()) {
            $className = NoteEditFormHandler::class;
            $repo = $this->noteRepo;
        } else {
            $className = EventEditFormHandler::class;
            $repo = $this->eventRepo;
        }

        return [$className, $repo];
    }

    /**
     * @return Event|Session|Space|NoteData
     */
    private function getData(ObjectRepository $repo, FocusData $focus)
    {
        $create = $focus->isCreate();
        if ($focus->isEvent()) {
            $data = $create ? new Event() : $repo->find($focus->getEventId());
        } elseif ($focus->isSession()) {
            $data = $create ? new Session() : $repo->find($focus->getSessionId());
        } elseif ($focus->isSpace()) {
            $data = $create ? new Space() : $repo->find($focus->getSpaceId());
        } elseif ($focus->isNote()) {
            /** @var Note $entity */
            $entity = $create ? new Note() : $repo->find($focus->getNoteId());
            $data = NoteData::createFromEntity($entity);
        } else {
            $data = Event::create();
        }

        return $data;
    }
}
