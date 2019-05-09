<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form;

use Maintainerati\Bikeshed\DataTransfer\NoteData;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class NoteEditFormType extends AbstractType implements DataMapperInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var RequestStack */
    private $requestStack;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, ['label' => false])
            ->add('date', HiddenType::class)
            ->add('space', HiddenType::class)
            ->add('note', TextAreaType::class)
            ->add('save', SubmitType::class)
            ->setDataMapper($this)
        ;
        $builder->setAction($this->getAction());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NoteData::class,
            'empty_data' => null,
        ]);
    }

    /**
     * @param NoteData $data
     */
    public function mapDataToForms($data, $forms): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        $forms['id']->setData($data->getId());
        $forms['date']->setData($data->getDate()->format('Y-m-d H:i:s'));
        $forms['space']->setData($data->getSpace());
        $forms['note']->setData($data->getNote());
    }

    /**
     * @param NoteData $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        if ($forms['id']->getData()) {
            $data->setId(Uuid::fromString($forms['id']->getData()));
        }
        if ($forms['space']->getData()) {
            $data->setSpace(Uuid::fromString($forms['space']->getData()));
        }
        $data->setNote($forms['note']->getData());
    }

    private function getAction(): object
    {
        return new class($this->urlGenerator, $this->requestStack) {
            private $urlGenerator;
            private $requestStack;

            public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
            {
                $this->urlGenerator = $urlGenerator;
                $this->requestStack = $requestStack;
            }

            public function __toString()
            {
                return $this->toString();
            }

            private function toString()
            {
                $request = $this->requestStack->getMasterRequest();
                if ($request->attributes->get('_route') !== 'bikeshed_async_form') {
                    return '';
                }
                $parameters = [
                    'event' => $request->attributes->get('event'),
                    'session' => $request->attributes->get('session'),
                    'space' => $request->attributes->get('space'),
                    'note' => $request->attributes->get('note'),
                ];

                try {
                    return $this->urlGenerator->generate('bikeshed_async_form', $parameters);
                } catch (\Exception $e) {
                    return '';
                }
            }
        };
    }
}
