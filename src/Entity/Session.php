<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"session:read"}}
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
 *     normalizationContext={"groups"={"session:read"}}
 * )
 * @ORM\Entity(repositoryClass="Maintainerati\Bikeshed\Repository\SessionRepository")
 */
class Session
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"session:read", "event:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="sessions")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @Groups({"session:read"})
     */
    private $event;

    /**
     * @ORM\Column(type="time")
     * @Groups({"session:read", "event:read"})
     */
    private $startTime;

    /**
     * @ORM\Column(type="time")
     * @Groups({"session:read", "event:read"})
     */
    private $endTime;

    /**
     * @var Collection|Space[]
     * @ORM\OneToMany(
     *     targetEntity="Space",
     *     mappedBy="session",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     * @ORM\OrderBy({"name"="ASC"})
     * @Groups({"session:read"})
     */
    private $spaces;

    public function __construct()
    {
        $this->spaces = new ArrayCollection();
    }

    public static function create(): self
    {
        return new self();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return Collection|Space[]
     */
    public function getSpaces(): Collection
    {
        return $this->spaces;
    }

    public function addSpace(Space $space): self
    {
        if (!$this->spaces->contains($space)) {
            $this->spaces[] = $space;
            $space->setSession($this);
        }

        return $this;
    }

    public function removeSpace(Space $space): self
    {
        if ($this->spaces->contains($space)) {
            $this->spaces->removeElement($space);
            if ($space->getSession() === $this) {
                $space->setSession(null);
            }
        }

        return $this;
    }
}
