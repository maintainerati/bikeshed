<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Form;

use Maintainerati\Bikeshed\Entity\Attendee;
use Maintainerati\Bikeshed\Validator\Constraints as BikeshedAssert;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    /** @var BikeshedAssert\ValidOneTimeKey */
    private $constraint;

    public function __construct(BikeshedAssert\ValidOneTimeKey $constraint)
    {
        $this->constraint = $constraint;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'oneTimeKey',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Unique one-time registration key',
                    ],
                    'constraints' => [
                        $this->constraint,
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => [
                        'placeholder' => 'Enter your email address',
                    ],
                ]
            )
            ->add(
                'handle',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Name that you would like to be publicly shown as',
                    ],
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Your first name (optional)',
                    ],
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Your last name (optional)',
                    ],
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new RollerworksConstraint\PasswordStrength([
                            'minLength' => 6,
                            'minStrength' => 3,
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => 'A decent password',
                    ],
                ]
            )
            ->add(
                'register',
                SubmitType::class
            )
        ;
    }

    /**
     * @var ?string
     * @ Assert\NotBlank()
     * @RollerworksConstraint\PasswordStrength(
     *     minLength=5,
     *     minStrength=3,
     *     message="Password not complex enough (at least one lower, one capital, and one number)."
     * )
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attendee::class,
            'error_bubbling' => true,
        ]);
    }
}
