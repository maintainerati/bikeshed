<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Maintainerati\Bikeshed\Entity\Event;
use Maintainerati\Bikeshed\Form\EventEditFormType;
use Maintainerati\Bikeshed\Repository\SessionRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventEditFormHandler implements HandlerTypeInterface
{
    /** @var SessionRepository */
    private $repo;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        SessionRepository $repo,
        EntityManagerInterface $em
    ) {
        $this->repo = $repo;
        $this->em = $em;
    }

    public function configure(HandlerConfigInterface $config): void
    {
        $config->setType(EventEditFormType::class);
        $config->onSuccess(function (Event $data, FormInterface $form, Request $request): void {
            $this->em->persist($data);
            $this->em->flush();
        });
    }
}
