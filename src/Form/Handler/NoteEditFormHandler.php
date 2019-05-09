<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use HTMLPurifier;
use Maintainerati\Bikeshed\DataTransfer\NoteData;
use Maintainerati\Bikeshed\Entity\Attendee;
use Maintainerati\Bikeshed\Entity\Note;
use Maintainerati\Bikeshed\Form\NoteEditFormType;
use Maintainerati\Bikeshed\Markdown\Markdown;
use Maintainerati\Bikeshed\Repository\AttendeeRepository;
use Maintainerati\Bikeshed\Repository\NoteRepository;
use Maintainerati\Bikeshed\Repository\SpaceRepository;
use Parsedown;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class NoteEditFormHandler implements HandlerTypeInterface
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var Environment */
    private $twig;
    /** @var AttendeeRepository */
    private $attendeeRepository;
    /** @var SpaceRepository */
    private $spaceRepository;
    /** @var NoteRepository */
    private $noteRepository;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var HTMLPurifier */
    private $purifier;
    /** @var Parsedown */
    private $markdown;

    public function __construct(
        AttendeeRepository $attendeeRepository,
        SpaceRepository $spaceRepository,
        NoteRepository $noteRepository,
        EntityManagerInterface $em,
        Environment $twig,
        TokenStorageInterface $tokenStorage,
        HTMLPurifier $purifier,
        Markdown $markdown
    ) {
        $this->attendeeRepository = $attendeeRepository;
        $this->spaceRepository = $spaceRepository;
        $this->noteRepository = $noteRepository;
        $this->em = $em;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->purifier = $purifier;
        $this->markdown = $markdown;
    }

    public function configure(HandlerConfigInterface $config): void
    {
        $config->setType(NoteEditFormType::class);
        $config->onSuccess(function (NoteData $data, FormInterface $form, Request $request): ?Response {
            $source = $request->getSession()->get('edit_origin');

            $note = $data->getId() ? $this->noteRepository->find($data->getId()) : null;
            if (!$note) {
                /** @var Attendee $attendee */
                $attendee = $this->tokenStorage->getToken()->getUser();
                $space = $this->spaceRepository->find($data->getSpace());
                $note = new Note();
                $note
                    ->setDate(new \DateTimeImmutable())
                    ->setAttendee($attendee)
                    ->setSpace($space)
                ;
            }
            $note->setNote($this->purifier->purify($data->getNote()));

            $this->em->persist($note);
            $this->em->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response($this->markdown->parse($note->getNote()));
            }

            return $source ? new RedirectResponse($source) : null;
        });
    }
}
