<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\DataTransfer;

use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class FocusData implements JsonSerializable
{
    public const KEY = 'bikeshed-focus';

    public const TYPE_EVENT = 'event';
    public const TYPE_SESSION = 'session';
    public const TYPE_SPACE = 'space';
    public const TYPE_NOTE = 'note';

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private $eventId;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private $sessionId;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private $spaceId;

    /** @var string|null */
    private $noteId;

    /** @var bool */
    private $create = false;

    private function __construct(bool $create)
    {
        $this->create = $create;
    }

    public static function create(bool $create): self
    {
        return new self($create);
    }

    public function isFocused(): bool
    {
        return $this->eventId && $this->sessionId && $this->spaceId;
    }

    public function isCreate(): bool
    {
        return $this->create;
    }

    public function getEventId(): ?string
    {
        return $this->eventId;
    }

    public function setEventId(?string $eventId): self
    {
        self::assertUuidOrNull($eventId);
        $this->eventId = $eventId;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId): self
    {
        self::assertUuidOrNull($sessionId);
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getSpaceId(): ?string
    {
        return $this->spaceId;
    }

    public function setSpaceId(?string $spaceId): self
    {
        self::assertUuidOrNull($spaceId);
        $this->spaceId = $spaceId;

        return $this;
    }

    public function getNoteId(): ?string
    {
        return $this->noteId;
    }

    public function setNoteId(?string $noteId): self
    {
        self::assertUuidOrNull($noteId);
        $this->noteId = $noteId;

        return $this;
    }

    public function getActive(): ?string
    {
        $type = $this->getType();
        $param = "{$type}Id";

        return $this->$param;
    }

    public function getFormName(?string $verb = 'edit'): ?string
    {
        $id = $this->getActive();

        return sprintf('%s%s%s_form', $this->getType(), $verb ? "_$verb" : '', $id ? "_$id" : '');
    }

    public function getType(): string
    {
        if ($this->eventId && $this->sessionId && $this->spaceId && $this->noteId) {
            return self::TYPE_NOTE;
        } elseif ($this->eventId && $this->sessionId && $this->spaceId) {
            return $this->create ? self::TYPE_NOTE : self::TYPE_SPACE;
        } elseif ($this->eventId && $this->sessionId) {
            return $this->create ? self::TYPE_SPACE : self::TYPE_SESSION;
        } elseif ($this->eventId) {
            return $this->create ? self::TYPE_SESSION : self::TYPE_EVENT;
        }
        $this->create = true;

        return self::TYPE_EVENT;
    }

    public function isEvent(): bool
    {
        return $this->getType() === self::TYPE_EVENT;
    }

    public function isSession(): bool
    {
        return $this->getType() === self::TYPE_SESSION;
    }

    public function isSpace(): bool
    {
        return $this->getType() === self::TYPE_SPACE;
    }

    public function isNote(): bool
    {
        return $this->getType() === self::TYPE_NOTE;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    private static function assertUuidOrNull(?string $uuid): void
    {
        if ($uuid === null) {
            return;
        }
        if (Uuid::isValid($uuid)) {
            return;
        }
        throw new \InvalidArgumentException(sprintf('UUID given is invalid: %s', $uuid));
    }
}
