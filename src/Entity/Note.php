<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"note:read"}}
 * )
 * @ ApiResource(
 *     attributes={
 *         "access_control"="is_granted('ROLE_USER')"
 *     },
 *     collectionOperations={
 *         "get",
 *         "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get",
 *         "post"={"access_control"="is_granted('ROLE_USER')"}
 *     },
 *     normalizationContext={"groups"={"note:read"}}
 * )
 * @ORM\Entity(repositoryClass="Maintainerati\Bikeshed\Repository\NoteRepository")
 */
class Note
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"note:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Groups({"note:read", "space:read"})
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Attendee", inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Groups({"note:read", "space:read"})
     */
    private $attendee;

    /**
     * @ORM\ManyToOne(targetEntity="Space", inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Groups({"note:read", "attendee:read"})
     */
    private $space;

    /**
     * @ORM\Column(type="text")
     * @Groups({"note:read", "attendee:read", "space:read"})
     */
    private $note;

    public function __construct()
    {
        $this->date = new DateTimeImmutable();
    }

    public static function create(): self
    {
        return new self();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAttendee(): ?Attendee
    {
        return $this->attendee;
    }

    public function setAttendee(Attendee $attendee): self
    {
        $this->attendee = $attendee;

        return $this;
    }

    public function getSpace(): ?Space
    {
        return $this->space;
    }

    public function setSpace(Space $space): self
    {
        $this->space = $space;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
