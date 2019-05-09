<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Loader;

use Maintainerati\Bikeshed\DataTransfer\FocusData;
use Maintainerati\Bikeshed\Entity\Event;
use Maintainerati\Bikeshed\Repository\EventRepository;
use Maintainerati\Bikeshed\Repository\SessionRepository;
use Maintainerati\Bikeshed\Repository\SpaceRepository;
use Psr\Cache\CacheItemPoolInterface;

final class FocusChoiceLoader
{
    /** @var string|null */
    private $eventId;
    /** @var string|null */
    private $sessionId;
    /** @var string|null */
    private $spaceId;

    /** @var EventRepository */
    private $eventRepo;
    /** @var SessionRepository */
    private $sessionRepo;
    /** @var SpaceRepository */
    private $spaceRepo;
    /** @var CacheItemPoolInterface */
    private $pool;

    public function __construct(
        EventRepository $eventRepo,
        SessionRepository $sessionRepo,
        SpaceRepository $spaceRepo,
        CacheItemPoolInterface $pool
    ) {
        $this->eventRepo = $eventRepo;
        $this->sessionRepo = $sessionRepo;
        $this->spaceRepo = $spaceRepo;
        $this->pool = $pool;
    }

    public function applyFocus(FocusData $focus): void
    {
        $this->eventId = $focus->getEventId();
        $this->sessionId = $focus->getSessionId();
        $this->spaceId = $focus->getSpaceId();
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCurrentEvent(): ?Event
    {
        $item = $this->pool->getItem('focus_event');
        if ($item->isHit()) {
            return $item->get();
        }
        $item->set($this->eventRepo->findCurrent());

        return $item->get();
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function loadEvents(): array
    {
        $item = $this->pool->getItem('focus_events');
        if ($item->isHit()) {
            return $item->get();
        }
        $item->set($this->eventRepo->findAll());

        return $item->get();
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function loadSessions(): array
    {
        if ($this->eventId === null) {
            return [];
        }
        $item = $this->pool->getItem('focus_sesisons_' . $this->eventId);
        if ($item->isHit()) {
            return $item->get();
        }
        $item->set($this->sessionRepo->findBy(['event' => $this->eventId]));

        return $item->get();
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function loadSpaces(): array
    {
        if ($this->sessionId === null) {
            return [];
        }
        $item = $this->pool->getItem('focus_spaces_' . $this->eventId);
        if ($item->isHit()) {
            return $item->get();
        }
        $item->set($this->spaceRepo->findBy(['session' => $this->sessionId]));

        return $item->get();
    }

    public function setEventId(?string $eventId): self
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function setSessionId(?string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function setSpaceId(?string $spaceId): self
    {
        $this->spaceId = $spaceId;

        return $this;
    }
}
