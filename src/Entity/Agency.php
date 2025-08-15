<?php

// src/Entity/Agency.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(
    name: 'agencies',
    indexes: [
        new ORM\Index(name: 'idx_agency_slug', columns: ['slug']),
        new ORM\Index(name: 'idx_agency_status', columns: ['status']),
        new ORM\Index(name: 'idx_agency_city', columns: ['city']),
        new ORM\Index(name: 'idx_agency_created_at', columns: ['created_at']),
    ]
)]
#[UniqueEntity(fields: ['slug'], message: 'This slug already exists.')]
#[UniqueEntity(fields: ['email'], message: 'This email already exists.')]
#[ORM\HasLifecycleCallbacks]
class Agency extends AbstractEntity
{
    #[Gedmo\Versioned]
    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: 'Agency name is required')]
    #[Assert\Length(max: 100, maxMessage: 'Agency name cannot exceed {{ limit }} characters')]
    public ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Slug is required')]
    #[Assert\Regex(
        pattern: '/^[a-z0-9-]+$/',
        message: 'Slug can only contain lowercase letters, numbers and hyphens'
    )]
    public ?string $slug = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email address')]
    public ?string $email = null;

    #[Gedmo\Versioned]
    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(
        pattern: '/^(\+\d{1,3})?\s?(\d[\s\-\.]?){6,14}\d$/',
        message: 'Invalid phone number format'
    )]
    public ?string $phoneNumber = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Address is required')]
    public ?string $address = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'City is required')]
    public ?string $city = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Choice(
        choices: ['active', 'inactive', 'suspended'],
        message: 'Invalid status'
    )]
    public string $status = 'active';

    #[ORM\Column(type: Types::JSON, nullable: true)]
    public ?array $settings = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'agency', cascade: ['persist'])]
    public Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->settings = [];
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
        return 'active' === $this->status;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
