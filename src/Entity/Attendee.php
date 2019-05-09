<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"attendee:read"}}
 * )
 * @ ApiResource(
 *     attributes={
 *         "access_control"="is_granted('ROLE_USER')"
 *     },
 *     collectionOperations={
 *         "get"={"access_control"="is_granted('ROLE_ADMIN')"},
 *         "post"={"access_control"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get"={"access_control"="is_granted('ROLE_USER')"},
 *         "post"={"access_control"="is_granted('ROLE_USER')"}
 *     },
 *     normalizationContext={"groups"={"attendee:read"}}
 * )
 * @ORM\Entity(repositoryClass="Maintainerati\Bikeshed\Repository\AttendeeRepository")
 * @UniqueEntity(fields={"email", }, message="There is already an account with this email address")
 * @UniqueEntity(fields={"handle"}, message="There is already an account with this handle")
 */
class Attendee implements UserInterface
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"attendee:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Groups({"attendee:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"attendee:read", "note:read", "space:read"})
     */
    private $handle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"attendee:read"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"attendee:read"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var ?string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @var ?string
     */
    private $plainPassword;

    /**
     * @var ?DateTimeInterface
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $passwordExpires;

    /**
     * @var string|null
     */
    private $oneTimeKey;

    /**
     * @var Collection|Space[]
     * @ORM\ManyToMany(
     *     targetEntity="Space",
     *     inversedBy="attendees"
     * )
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"attendee:read"})
     */
    private $spaces;

    /**
     * @var Collection|Note[]
     * @ORM\OneToMany(
     *     targetEntity="Note",
     *     mappedBy="attendee",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     * )
     * @Groups({"attendee:read"})
     */
    private $notes;

    public function __construct()
    {
        $this->spaces = new ArrayCollection();
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

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getHandle(): ?string
    {
        return $this->handle;
    }

    public function setHandle(string $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function hasRole($role): bool
    {
        return \in_array($role, $this->getRoles(), true);
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordExpires(): ?DateTimeInterface
    {
        return $this->passwordExpires;
    }

    public function setPasswordExpires(DateTimeInterface $passwordExpires): self
    {
        $this->passwordExpires = $passwordExpires;

        return $this;
    }

    /** @internal */
    public function getOneTimeKey(): ?string
    {
        return $this->oneTimeKey;
    }

    /** @internal */
    public function setOneTimeKey(?string $oneTimeKey): void
    {
        $this->oneTimeKey = $oneTimeKey;
    }

    /**
     * @return Collection|Space[]
     */
    public function getSpaces()
    {
        return $this->spaces;
    }

    public function addSpace(Space $space): self
    {
        if (!$this->spaces->contains($space)) {
            $this->spaces[] = $space;
            $space->addAttendee($this);
        }

        return $this;
    }

    public function removeSpace(Space $space): self
    {
        if ($this->spaces->contains($space)) {
            $this->spaces->removeElement($space);
            $space->removeAttendee($this);
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
            $note->setAttendee($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            if ($note->getAttendee() === $this) {
                $note->setAttendee(null);
            }
        }

        return $this;
    }

    public function getSalt(): void
    {
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
