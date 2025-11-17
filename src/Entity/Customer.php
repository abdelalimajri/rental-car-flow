<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Trait\AgencyTrait;
use App\Enum\Gender;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use App\ApiPlatform\Filter\GlobalSearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'customers')]

#[ApiResource(
    paginationItemsPerPage: 15,
    paginationMaximumItemsPerPage: 30,
    paginationClientItemsPerPage: true
)]
#[ApiFilter(SearchFilter::class, properties: [
    'lastName' => 'ipartial',
    'firstName' => 'ipartial',
    'email' => 'ipartial',
    'identityNumber' => 'ipartial',
    'drivingLicenseNumber' => 'ipartial',
    'phoneNumber' => 'ipartial',
    'gender' => 'exact',
])]
#[ApiFilter(BooleanFilter::class, properties: ['active'])]
#[ApiFilter(OrderFilter::class, properties: [
    'lastName', 'firstName', 'email', 'birthDate', 'createdAt',
    'identityNumber', 'phoneNumber', 'active'
])]
#[ApiFilter(DateFilter::class, properties: ['birthDate', 'createdAt'])]
#[ApiFilter(GlobalSearchFilter::class, properties: [
    'fields' => ['lastName','firstName','email','identityNumber','phoneNumber']
])]
#[UniqueEntity(fields: ['email', 'agency'], message: 'Cet email existe déjà dans cette agence.')]
#[UniqueEntity(fields: ['identityNumber', 'agency'], message: 'Ce numéro d\'identité existe déjà dans cette agence.')]
#[UniqueEntity(fields: ['drivingLicenseNumber', 'agency'], message: 'Ce numéro de permis de conduire existe déjà dans cette agence.')]
class Customer extends AbstractEntity
{
    use AgencyTrait;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    public string $firstName;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de famille est obligatoire.')]
    public string $lastName;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le numéro d\'identité est obligatoire.')]
    public string $identityNumber;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le numéro de permis de conduire est obligatoire.')]
    public string $drivingLicenseNumber;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $licenseExpirationDate;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $birthDate;

    #[Gedmo\Versioned]
    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^(\+\d{1,3})?\s?(\d[\s\-\.]?){6,14}\d$/',
        message: 'Format de numéro de téléphone invalide.'
    )]
    public string $phoneNumber;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'Adresse email invalide.')]
    public ?string $email;

    #[Gedmo\Versioned]
    #[ORM\Column]
    public bool $active;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $address;

    #[ORM\Column(type: 'string', enumType: Gender::class)]
    public Gender $gender;

    public function canDrive(): bool
    {
        return $this->active
            && $this->licenseExpirationDate instanceof \DateTimeImmutable
            && $this->licenseExpirationDate > new \DateTimeImmutable();
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getAge(): ?int
    {
        if (!$this->birthDate instanceof \DateTimeImmutable) {
            return null; // naissance inconnue
        }
        $today = new \DateTimeImmutable('today');
        return $this->birthDate->diff($today)->y;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }
}
