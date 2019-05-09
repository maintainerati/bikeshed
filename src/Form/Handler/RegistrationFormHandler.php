<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Maintainerati\Bikeshed\Entity\Attendee;
use Maintainerati\Bikeshed\Form\RegistrationFormType;
use Maintainerati\Bikeshed\Repository\OneTimeKeyRepository;
use Maintainerati\Bikeshed\Security\FormAuthenticator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

final class RegistrationFormHandler implements HandlerTypeInterface
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var GuardAuthenticatorHandler */
    private $guardHandler;
    /** @var FormAuthenticator */
    private $authenticator;
    /** @var OneTimeKeyRepository */
    private $oneTimeKeyRepo;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        FormAuthenticator $authenticator,
        OneTimeKeyRepository $oneTimeKeyRepo,
        EntityManagerInterface $em
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->guardHandler = $guardHandler;
        $this->authenticator = $authenticator;
        $this->oneTimeKeyRepo = $oneTimeKeyRepo;
        $this->em = $em;
    }

    public function configure(HandlerConfigInterface $config): void
    {
        $config->setType(RegistrationFormType::class);
        $config->onSuccess(function (Attendee $data, FormInterface $form, Request $request): Response {
            $data->setPassword($this->passwordEncoder->encodePassword($data, $form->get('plainPassword')->getData()));
            $data->setRoles(['ROLE_USER']);

            $otk = $this->oneTimeKeyRepo->findOneBy(['oneTimeKey' => $data->getOneTimeKey()]);

            $this->em->remove($otk);
            $this->em->persist($data);
            $this->em->flush();

            // TODO: Send an email

            return $this->guardHandler->authenticateUserAndHandleSuccess($data, $request, $this->authenticator, 'bikeshed');
        });
    }
}
