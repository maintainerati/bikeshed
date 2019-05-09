<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\DataTransfer;

use Maintainerati\Bikeshed\Entity\Attendee;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;
use Symfony\Component\Validator\Constraints as Assert;

final class AttendeeData
{
    /**
     * @var ?string
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * @var ?string
     * @Assert\Email(
     *     strict=true,
     *     checkHost=true
     * )
     */
    private $email;

    /**
     * @var ?string
     */
    private $firstName;

    /**
     * @var ?string
     */
    private $lastName;

    /**
     * @var ?string
     * @RollerworksPassword\PasswordStrength(
     *     minLength=8,
     *     minStrength=4,
     *     message="Password not complex enough (at least one lower, one capital, and one number)."
     * )
     */
    private $password;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public static function createFromEntity(Attendee $entity): self
    {
        $self = new self();

        $self->id = $entity->getId();
        $self->email = $entity->getEmail();
        $self->firstName = $entity->getFirstName();
        $self->lastName = $entity->getLastName();

        return $self;
    }
}
