<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Responder;

use Maintainerati\Bikeshed\DataTransfer\FocusFactory;
use Maintainerati\Bikeshed\Repository\AttendeeRepository;
use Maintainerati\Bikeshed\Repository\EventRepository;
use Maintainerati\Bikeshed\Util\GitHubEmoji;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class HomepageResponder
{
    /** @var AttendeeRepository */
    private $attendeeRepository;
    /** @var EventRepository */
    private $eventRepository;
    /** @var FocusFactory */
    private $focusFactory;
    /** @var Environment */
    private $twig;
    /** @var GitHubEmoji */
    private $gitHub;

    public function __construct(
        AttendeeRepository $attendeeRepository,
        EventRepository $eventRepository,
        FocusFactory $focusFactory,
        Environment $twig,
        GitHubEmoji $gitHubEmoji
    ) {
        $this->attendeeRepository = $attendeeRepository;
        $this->eventRepository = $eventRepository;
        $this->focusFactory = $focusFactory;
        $this->twig = $twig;
        $this->gitHub = $gitHubEmoji;
    }

    /**
     * Return current attendee and all events.
     */
    public function getDefault(): Response
    {
        $html = $this->twig->render('@Bikeshed/homepage/index.html.twig', [
            'focus' => $this->focusFactory->createFromSession(false),
            'attendee' => $this->attendeeRepository->findOneBy([]),
            'events' => $this->eventRepository->findBy([], null, 2),
            'emoji' => $this->gitHub->getJson(),
        ]);

        return new Response($html);
    }
}
