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
 *     normalizationContext={"groups"={"event:read"}}
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
 *         "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     normalizationContext={"groups"={"event:read"}}
 * )
 * @ORM\Entity(repositoryClass="Maintainerati\Bikeshed\Repository\EventRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(
 *     name="u_event_idx",
 *     columns={"date", "country", "city", "location"}
 * )})
 */
class Event
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"event:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"event:read", "session:read"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event:read", "session:read"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event:read", "session:read"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"event:read", "session:read"})
     */
    private $location;

    /**
     * @var Collection|Session[]
     * @ORM\OneToMany(
     *     targetEntity="Session",
     *     mappedBy="event",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     * @ORM\OrderBy({"startTime"="ASC"})
     * @Groups({"event:read"})
     */
    private $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public static function create(): self
    {
        return new self();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setEvent($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            if ($session->getEvent() === $this) {
                $session->setEvent(null);
            }
        }

        return $this;
    }
}
