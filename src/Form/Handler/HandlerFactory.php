<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form\Handler;

use Hostnet\Component\FormHandler\FormSubmitProcessor;
use Hostnet\Component\FormHandler\HandlerBuilder;
use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Hostnet\Component\FormHandler\HandlerRegistryInterface;
use Hostnet\Component\FormHandler\HandlerTypeAdapter;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class HandlerFactory implements HandlerFactoryInterface
{
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var HandlerRegistryInterface */
    private $registry;

    public function __construct(FormFactoryInterface $formFactory, HandlerRegistryInterface $registry)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function create($class, FormFactoryInterface $formFactory = null): NamedHandlerInterface
    {
        return $this->getHandler($formFactory ?: $this->formFactory, $this->registry->getType($class));
    }

    private function getHandler(FormFactoryInterface $formFactory, HandlerTypeInterface $handlerType): NamedHandlerInterface
    {
        $class = new class($formFactory, $handlerType) implements NamedHandlerInterface {
            /** @var FormSubmitProcessor */
            private $submitProcessor;
            /** @var FormFactoryInterface */
            private $formFactory;
            /** @var HandlerTypeInterface */
            private $handlerType;

            public function __construct(FormFactoryInterface $formFactory, HandlerTypeInterface $handlerType)
            {
                $this->formFactory = $formFactory;
                $this->handlerType = $handlerType;
            }

            /** {@inheritdoc} */
            public function getForm()
            {
                if ($this->submitProcessor === null) {
                    throw new \LogicException('Cannot retrieve form when it has not been handled.');
                }

                return $this->submitProcessor->getForm();
            }

            /** {@inheritdoc} */
            public function handle(Request $request, $data = null, ?string $formName = null)
            {
                if ($this->handlerType instanceof HandlerTypeAdapter) {
                    $data = $this->handlerType->syncData($data);
                }

                $builder = new HandlerBuilder();
                $builder->setName($formName);
                $this->handlerType->configure($builder);

                $this->submitProcessor = $builder->build($this->formFactory, $data);

                return $this->submitProcessor->process($request);
            }
        };

        return $class;
    }
}
