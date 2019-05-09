<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Controller;

use DateTimeImmutable;
use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Maintainerati\Bikeshed\DataTransfer\FocusData;
use Maintainerati\Bikeshed\DataTransfer\NoteData;
use Maintainerati\Bikeshed\Entity\Attendee;
use Maintainerati\Bikeshed\Entity\Note;
use Maintainerati\Bikeshed\Form\Handler\NoteEditFormHandler;
use Maintainerati\Bikeshed\Repository\NoteRepository;
use Maintainerati\Bikeshed\Repository\SpaceRepository;
use Maintainerati\Bikeshed\Util\GitHubEmoji;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpaceController extends AbstractController
{
    /** @var HandlerFactoryInterface */
    private $factory;

    public function __construct(HandlerFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request, SpaceRepository $spaceRepo, NoteRepository $noteRepo, GitHubEmoji $github): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Attendee tried to access a page without authentication');

        $userSession = $request->getSession();
        $focus = $userSession->get(FocusData::KEY);
        if (!$focus || !$focus instanceof FocusData || !$focus->isFocused()) {
            return $this->redirect('focus');
        }

        $space = $spaceRepo->find($focus->getSpaceId());
        $formData = $request->request->get('note_edit_form');
        if ($formData['id'] ?? false) {
            $entity = $noteRepo->find($formData['id']);
            $attendee = $entity->getAttendee();
            if ($attendee->getId() !== $this->getUser()->getId()) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Attendee tried to edit a note they did not own.');
            }
        } else {
            /** @var Attendee $attendee */
            $attendee = $this->getUser();
            $entity = new Note();
            $entity->setDate(new DateTimeImmutable());
            $entity->setAttendee($attendee);
            $entity->setSpace($space);
        }
        $data = NoteData::createFromEntity($entity);

        $handler = $this->factory->create(NoteEditFormHandler::class);
        $response = $handler->handle($request, $data);
        if ($response instanceof Response) {
            return $response;
        }

        return $this->render('@Bikeshed/space/index.html.twig', [
            'focus' => $focus,
            'space' => $space,
            'notes' => $noteRepo->findBy(['space' => $focus->getSpaceId()]),
            'form' => $handler->getForm()->createView(),
            'emoji' => $github->getJson(),
        ]);
    }
}
