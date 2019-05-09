<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\DataTransfer;

use DateTimeInterface;
use Maintainerati\Bikeshed\Entity\Note;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class NoteData
{
    /**
     * @var ?UuidInterface
     * @Assert\Uuid()
     */
    private $id;

    /**
     * @var ?DateTimeInterface
     * @Assert\Date()
     */
    private $date;

    /**
     * @var ?UuidInterface
     * @Assert\Uuid()
     */
    private $attendee;

    /**
     * @var ?UuidInterface
     * @Assert\Uuid()
     */
    private $space;

    /**
     * @var ?string
     * @Assert\NotBlank()
     */
    private $note;

    public function __construct(?string $space = null)
    {
        $this->space = $space;
    }

    public static function create(string $space = null): self
    {
        return new self($space);
    }

    public static function createFromIterable(?iterable $values): self
    {
        $self = new self();
        foreach ($self as $property => $v) {
            $self->$property = ($values[$property] ?? null) ?: null;
        }

        return $self;
    }

    public static function createFromEntity(Note $note): self
    {
        $self = new self();
        $self->id = $note->getId();
        $self->date = $note->getDate();
        if ($note->getAttendee()) {
            $self->attendee = $note->getAttendee()->getId();
        }
        if ($note->getSpace()) {
            $self->space = $note->getSpace()->getId();
        }
        $self->note = $note->getNote();

        return $self;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(?UuidInterface $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAttendee(): ?UuidInterface
    {
        return $this->attendee;
    }

    public function setAttendee(?UuidInterface $attendee): self
    {
        $this->attendee = $attendee;

        return $this;
    }

    public function getSpace(): ?UuidInterface
    {
        return $this->space;
    }

    public function setSpace(?UuidInterface $space): self
    {
        $this->space = $space;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
