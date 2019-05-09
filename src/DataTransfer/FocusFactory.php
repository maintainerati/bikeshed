<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\DataTransfer;

use Maintainerati\Bikeshed\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class FocusFactory
{
    /** @var EventRepository */
    private $repo;
    /** @var SessionInterface */
    private $session;

    public function __construct(EventRepository $repo, SessionInterface $session)
    {
        $this->repo = $repo;
        $this->session = $session;
    }

    public function createDefault(bool $create): FocusData
    {
        $focus = FocusData::create($create);
        $event = $this->repo->findCurrent();
        $focus->setEventId($event ? (string) $event->getId() : null);

        return $focus;
    }

    public function createFromSession(bool $create): FocusData
    {
        return $this->session->get(FocusData::KEY) ?: $this->createDefault($create);
    }

    public function createFromRequestAttributes(Request $request): FocusData
    {
        $create = $request->query->getBoolean('create');
        $focus = FocusData::create($create);
        $focus
            ->setEventId($request->attributes->get('event'))
            ->setSessionId($request->attributes->get('session'))
            ->setSpaceId($request->attributes->get('space'))
            ->setNoteId($request->attributes->get('note'))
        ;

        return $focus;
    }

    public function createFromRequestForm(Request $request): FocusData
    {
        $form = $request->request->get('focus_form');
        $focus = $this->createDefault(false);
        if ($form['eventId'] ?? null) {
            $focus->setEventId($form['eventId']);
        }
        if ($form['sessionId'] ?? null) {
            $focus->setSessionId($form['sessionId']);
        }
        if ($form['spaceId'] ?? null) {
            $focus->setSpaceId($form['spaceId']);
        }
        if ($form['noteId'] ?? null) {
            $focus->setNoteId($form['noteId']);
        }

        return $focus;
    }

    public function createFromIterable(?iterable $values): FocusData
    {
        return $this->createDefault(false)
            ->setEventId($values['event'] ?? null)
            ->setSessionId($values['session'] ?? null)
            ->setSpaceId($values['space'] ?? null)
            ->setNoteId($values['note'] ?? null)
        ;
    }
}
