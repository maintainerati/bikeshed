<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Maintainerati\Bikeshed\Entity\Space;
use Maintainerati\Bikeshed\Form\SpaceEditFormType;
use Maintainerati\Bikeshed\Repository\SpaceRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class SpaceEditFormHandler implements HandlerTypeInterface
{
    /** @var SpaceRepository */
    private $repo;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        SpaceRepository $repo,
        EntityManagerInterface $em
    ) {
        $this->repo = $repo;
        $this->em = $em;
    }

    public function configure(HandlerConfigInterface $config): void
    {
        $config->setType(SpaceEditFormType::class);
        $config->onSuccess(function (Space $data, FormInterface $form, Request $request): void {
            $this->em->persist($data);
            $this->em->flush();
        });
    }
}
