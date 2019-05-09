<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form;

use DateTime;
use DateTimeInterface;
use Maintainerati\Bikeshed\DataTransfer\FocusData;
use Maintainerati\Bikeshed\Entity\Event;
use Maintainerati\Bikeshed\Entity\Session;
use Maintainerati\Bikeshed\Entity\Space;
use Maintainerati\Bikeshed\Form\Loader\FocusChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FocusFormType extends AbstractType implements DataMapperInterface
{
    /** @var FocusChoiceLoader */
    private $choiceLoader;

    public function __construct(FocusChoiceLoader $choiceLoader)
    {
        $this->choiceLoader = $choiceLoader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'eventId',
                ChoiceType::class,
                [
                    'choice_loader' => new CallbackChoiceLoader(function () {
                        return $this->choiceLoader->loadEvents();
                    }),
                    'choice_value' => function ($entity = null) {
                        if ($entity === null) {
                            $entity = $this->choiceLoader->getCurrentEvent();
                        }

                        return $entity === null ? '' : ($entity instanceof Event ? $entity->getId() : $entity);
                    },
                    'choice_label' => function (Event $event) {
                        return sprintf(
                            '%s, %s %s',
                            $event->getDate()->format('j M'),
                            $event->getLocation(),
                            $event->getCity()
                        );
                    },
                    'placeholder' => 'Choose an event',
                    'group_by' => function ($choice, $key, $value) {
                        /** @var Event $choice */
                        return Countries::getName($choice->getCountry());
                    },
                ]
            )
            ->add(
                'sessionId',
                ChoiceType::class,
                [
                    'choice_loader' => new CallbackChoiceLoader(function () {
                        return $this->choiceLoader->loadSessions();
                    }),
                    'choice_value' => function (Session $entity = null) {
                        return $entity ? $entity->getId() : '';
                    },
                    'choice_label' => function (Session $event) {
                        return sprintf(
                            '%s - %s',
                            $event->getStartTime()->format('H:i'),
                            $event->getEndTime()->format('H:i')
                        );
                    },
                    'placeholder' => 'Choose a session',
                    'group_by' => function ($choice, $key, $value) {
                        /** @var Session $choice */
                        return $this->getSlot(new DateTime($choice->getStartTime()->format('H:i')));
                    },
                ]
            )
            ->add(
                'spaceId',
                ChoiceType::class,
                [
                    'choice_loader' => new CallbackChoiceLoader(function () {
                        return $this->choiceLoader->loadSpaces();
                    }),
                    'choice_value' => function (Space $entity = null) {
                        return $entity ? $entity->getId() : '';
                    },
                    'choice_label' => 'name',
                    'placeholder' => 'Choose a space',
                ]
            )
            ->add('join', SubmitType::class)
            ->setDataMapper($this)
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (PreSetDataEvent $formEvent): void {
                /** @var FocusData $data */
                $data = $formEvent->getData();
                $this->choiceLoader->applyFocus($data);
                //$this->sessionModifier($formEvent->getForm(), $data);
                //$this->spaceModifier($formEvent->getForm(), $data);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FocusData::class,
            'empty_data' => null,
        ]);
    }

    public function mapDataToForms($data, $forms): void
    {
        //$forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
    }

    /**
     * @param FocusData $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        if ($forms['eventId']) {
            $data->setEventId((string) $forms['eventId']->getData()->getId());
        }
        if (isset($forms['sessionId']) && $forms['sessionId']->getData()) {
            $data->setSessionId((string) $forms['sessionId']->getData()->getId());
        }
        if (isset($forms['spaceId']) && $forms['spaceId']->getData()) {
            $data->setSpaceId((string) $forms['spaceId']->getData()->getId());
        }
    }

    private function sessionModifier(FormInterface $form, FocusData $data = null): void
    {
        $eventId = $data === null ? [] : $data->getEventId();
        if (!$eventId) {
            $form->remove('sessionId');
        }
    }

    private function spaceModifier(FormInterface $form, FocusData $data = null): void
    {
        $sessionId = $data === null ? [] : $data->getSessionId();
        if (!$sessionId) {
            $form->remove('spaceId');
        }
    }

    private function getSlot(DateTimeInterface $start): string
    {
        $early = new DateTime('00:00');
        $morning = new DateTime('08:00');
        $afternoon = new DateTime('12:00');
        $evening = new DateTime('18:00');
        if ($start >= $morning && $start < $afternoon) {
            return 'Morning';
        } elseif ($start >= $afternoon && $start < $evening) {
            return 'Afternoon';
        } elseif ($start >= $evening && $start < $early) {
            return 'Evening';
        }

        return 'Early';
    }
}
