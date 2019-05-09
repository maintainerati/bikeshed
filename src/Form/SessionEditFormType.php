<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form;

use Maintainerati\Bikeshed\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SessionEditFormType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('event', HiddenType::class)
            ->add('startTime', TimeType::class, ['widget' => 'single_text'])
            ->add('endTime', TimeType::class, ['widget' => 'single_text'])
            ->add('save', SubmitType::class)
            ->setDataMapper($this)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
            'empty_data' => null,
        ]);
    }

    /**
     * @param Session $data
     */
    public function mapDataToForms($data, $forms): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        $forms['id']->setData($data->getId());
        $forms['startTime']->setData($data->getStartTime());
        $forms['endTime']->setData($data->getEndTime());
    }

    /**
     * @param Session $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        $data->setStartTime($forms['startTime']->getData());
        $data->setEndTime($forms['endTime']->getData());
    }
}
