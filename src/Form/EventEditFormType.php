<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form;

use Maintainerati\Bikeshed\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EventEditFormType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('date', DateType::class, ['widget' => 'single_text'])
            ->add(
                'country',
                CountryType::class,
                [
                    'placeholder' => 'Select a country',
                    'attr' => [
                        'placeholder' => 'Select a country',
                    ],
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'City',
                    ],
                ]
            )
            ->add(
                'location',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'Event location',
                    ],
                ]
            )
            ->add('save', SubmitType::class)
            ->setDataMapper($this)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'empty_data' => null,
        ]);
    }

    /**
     * @param Event $data
     */
    public function mapDataToForms($data, $forms): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        $forms['id']->setData($data->getId());
        $forms['date']->setData($data->getDate());

        $forms['country']->setData($data->getCountry());
        $forms['city']->setData($data->getCity());
        $forms['location']->setData($data->getLocation());
    }

    /**
     * @param Event $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        $data->setDate($forms['date']->getData());
        $data->setCountry($forms['country']->getData());
        $data->setCity($forms['city']->getData());
        $data->setLocation($forms['location']->getData());
    }
}
