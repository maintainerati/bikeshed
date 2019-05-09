<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"space:read"}}
 * )
 * @ ApiResource(
 *     attributes={
 *         "access_control"="is_granted('ROLE_USER')"
 *     },
 *     collectionOperations={
 *         "get"={"access_control"="is_granted('ROLE_USER')"},
 *         "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get"={"access_control"="is_granted('ROLE_USER')"},
 *         "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     normalizationContext={"groups"={"space:read"}}
 * )
 * @ORM\Entity(repositoryClass="Maintainerati\Bikeshed\Repository\SpaceRepository")
 */
class Space
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"space:read", "attendee:read", "note:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"space:read", "attendee:read", "note:read", "session:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"space:read", "attendee:read", "note:read", "session:read"})
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="spaces")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Groups({"space:read", "note:read"})
     */
    private $session;

    /**
     * @var Collection|Attendee[]
     * @ORM\ManyToMany(
     *     targetEntity="Attendee",
     *     mappedBy="spaces"
     * )
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"space:read"})
     */
    private $attendees;

    /**
     * @var Collection|Note[]
     * @ORM\OneToMany(
     *     targetEntity="Note",
     *     mappedBy="space",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     * @ORM\OrderBy({"date"="ASC"})
     * @Groups({"space:read"})
     */
    private $notes;

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public static function create(): self
    {
        return new self();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(?string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collection|Attendee[]
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    public function addAttendee(Attendee $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees[] = $attendee;
            $attendee->addSpace($this);
        }

        return $this;
    }

    public function removeAttendee(Attendee $attendee): self
    {
        if ($this->attendees->contains($attendee)) {
            $this->attendees->removeElement($attendee);
            $attendee->removeSpace($this);
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setSpace($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            if ($note->getSpace() === $this) {
                $note->setSpace(null);
            }
        }

        return $this;
    }
}
