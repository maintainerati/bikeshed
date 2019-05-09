<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Validator\Constraints;

use Maintainerati\Bikeshed\Repository\OneTimeKeyRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ValidOneTimeKeyValidator extends ConstraintValidator
{
    /** @var OneTimeKeyRepository */
    private $repo;

    public function __construct(OneTimeKeyRepository $repo)
    {
        $this->repo = $repo;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidOneTimeKey) {
            throw new UnexpectedTypeException($constraint, ValidOneTimeKey::class);
        }
        if ($value === null || $value === '') {
            return;
        }
        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }
        if (!Uuid::isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ key }}', $value)
                ->addViolation()
            ;
        } else {
            $entity = $this->repo->findOneBy(['oneTimeKey' => $value]);
            if (!$entity || $entity->hasExpired()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ key }}', $value)
                    ->addViolation()
                ;
            }
        }
    }
}
