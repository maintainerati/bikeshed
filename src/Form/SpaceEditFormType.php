<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form;

use Maintainerati\Bikeshed\Entity\Space;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SpaceEditFormType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('event', HiddenType::class)
            ->add('session', HiddenType::class)
            ->add('name', TextType::class)
            ->add('topic', TextType::class)
            ->add('save', SubmitType::class)
            ->setDataMapper($this)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Space::class,
            'empty_data' => null,
        ]);
    }

    /**
     * @param Space $data
     */
    public function mapDataToForms($data, $forms): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        $forms['id']->setData($data->getId());
        $forms['name']->setData($data->getName());
        $forms['topic']->setData($data->getTopic());
    }

    /**
     * @param Space $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);
        /** @var FormInterface[] $forms */
        $data->setName($forms['name']->getData());
        $data->setTopic($forms['topic']->getData());
    }
}
