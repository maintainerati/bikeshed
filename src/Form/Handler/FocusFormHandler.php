<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Handler;

use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Limenius\Liform\Liform;
use Maintainerati\Bikeshed\DataTransfer\FocusData;
use Maintainerati\Bikeshed\Form\FocusFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class FocusFormHandler implements HandlerTypeInterface
{
    /** @var Environment */
    private $twig;
    /** @var SessionInterface */
    private $session;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Liform */
    private $liform;

    public function __construct(Environment $twig, SessionInterface $session, UrlGeneratorInterface $urlGenerator, Liform $liform)
    {
        $this->twig = $twig;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->liform = $liform;
    }

    public function configure(HandlerConfigInterface $config): void
    {
        $config->setType(FocusFormType::class);
        $config->onSuccess(function (FocusData $data, FormInterface $form, Request $request): ?Response {
            if ($data->isFocused()) {
                $this->session->set(FocusData::KEY, $data);

                return new RedirectResponse($this->urlGenerator->generate('bikeshed_space'));
            }
            if ($request->isXmlHttpRequest()) {
                return new Response($this->twig->render('@Bikeshed/focus/form.html.twig', [
                    'form' => $form->createView(),
                ]));
                //return new JsonResponse($this->liform->transform($form));
            }

            return null;
        });
    }
}
