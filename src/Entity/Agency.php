<?php
// src/Entity/Agency.php
namespace App\Entity;

use App\Repository\AgencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AgencyRepository::class)]
#[ORM\Table(
    name: 'agencies',
    indexes: [
        new ORM\Index(name: 'idx_agency_slug', columns: ['slug']),
        new ORM\Index(name: 'idx_agency_status', columns: ['status']),
        new ORM\Index(name: 'idx_agency_city', columns: ['city']),
        new ORM\Index(name: 'idx_agency_created_at', columns: ['created_at'])
    ]
)]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exists.')]
#[UniqueEntity(fields: ['email'], message: 'This email already exists.')]
#[ORM\HasLifecycleCallbacks]
class Agency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: 'Agency name is required')]
    #[Assert\Length(max: 100, maxMessage: 'Agency name cannot exceed {{ limit }} characters')]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Slug is required')]
    #[Assert\Regex(
        pattern: '/^[a-z0-9-]+$/', 
        message: 'Slug can only contain lowercase letters, numbers and hyphens'
    )]
    private ?string $slug = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email address')]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(
        pattern: '/^(\+212|0)[5-7]\d{8}$/', 
        message: 'Invalid Moroccan phone number format'
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Address is required')]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'City is required')]
    private ?string $city = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Choice(
        choices: ['active', 'inactive', 'suspended'], 
        message: 'Invalid status'
    )]
    private string $status = 'active';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $settings = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'agency', cascade: ['persist'])]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->settings = [];
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = strtolower($slug);
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): static
    {
        $this->settings = $settings;
        return $this;
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function setSetting(string $key, mixed $value): static
    {
        if ($this->settings === null) {
            $this->settings = [];
        }
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setAgency($this);
        }
        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getAgency() === $this) {
                $user->setAgency(null);
            }
        }
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get ID as string for URLs and forms
     */
    public function getIdString(): string
    {
        return (string) $this->id;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}