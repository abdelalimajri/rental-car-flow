<?php

namespace App\Entity;

use App\Entity\Trait\AgencyTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'This email already exists.')]
#[ORM\HasLifecycleCallbacks]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    use AgencyTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email address')]
    public ?string $email = null;

    #[ORM\Column]
    public ?string $password = null;

    #[ORM\Column(type: Types::JSON)]
    public array $roles = [];

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(max: 100, maxMessage: 'First name cannot exceed {{ limit }} characters')]
    public ?string $firstName = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(max: 100, maxMessage: 'Last name cannot exceed {{ limit }} characters')]
    public ?string $lastName = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Regex(
        pattern: '/^(\+212|0)[5-7]\d{8}$/',
        message: 'Invalid Moroccan phone number format'
    )]
    public ?string $phoneNumber = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(
        choices: ['active', 'inactive', 'suspended'],
        message: 'Invalid status'
    )]
    public string $status = 'active';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $lastLoginAt = null;

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): static
    {
        $this->roles = array_values(array_filter($this->roles, fn ($r) => $r !== $role));

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getFullName(): string
    {
        return trim($this->firstName.' '.$this->lastName);
    }

    public function isActive(): bool
    {
        return 'active' === $this->status;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function isAgencyUser(): bool
    {
        return null !== $this->agency && !$this->isSuperAdmin();
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
