<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="Maintainerati\Bikeshed\Repository\OneTimeKeyRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OneTimeKey
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private $oneTimeKey;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $validUntil;

    public function __construct(DateTimeInterface $validUntil)
    {
        $this->validUntil = $validUntil;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getOneTimeKey(): ?UuidInterface
    {
        return $this->oneTimeKey;
    }

    public function getValidUntil(): ?DateTimeInterface
    {
        return $this->validUntil;
    }

    public function hasExpired(): bool
    {
        return $this->validUntil < (new DateTimeImmutable())->setTime(0, 0, 0);
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->oneTimeKey = $this->oneTimeKey ?: Uuid::uuid4();
        $this->validUntil = $this->validUntil ?: (new DateTimeImmutable('+3 days'))->setTime(0, 0, 0);
        $limit = new DateTimeImmutable('+1 month');

        if ($this->validUntil > $limit) {
            throw new ORMInvalidArgumentException(sprintf(
                'Failed to persist %s::validUntil later than %s (1 month from today). %s was given.',
                self::class,
                $limit->format('Y-m-d'),
                $this->validUntil->format('Y-m-d')
            ));
        }
    }
}
