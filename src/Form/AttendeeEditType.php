<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form;

use Maintainerati\Bikeshed\DataTransfer\AttendeeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendeeEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'id',
                TextType::class,
                [
                    'disabled' => true,
                ]
            )
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => false,
                    'first_options' => ['label' => 'Password', 'attr' => ['placeholder' => 'Enter your password']],
                    'second_options' => ['label' => 'Repeat Password', 'attr' => ['placeholder' => 'Repeat your password']],
                ]
            )
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttendeeData::class,
        ]);
    }
}
