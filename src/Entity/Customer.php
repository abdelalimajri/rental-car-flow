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
class Customer extends AbstractEntity
{
    use AgencyTrait;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 255)]
    public string $firstName;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 255)]
    public string $lastName;

    #[ORM\Column(length: 20)]
    public string $identityNumber;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 20)]
    public string $drivingLicenseNumber;

    #[ORM\Column]
    public \DateTimeImmutable $licenseExpirationDate;

    #[ORM\Column]
    public \DateTimeImmutable $birthDate;

    #[Gedmo\Versioned]
    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(
        pattern: '/^(\+\d{1,3})?\s?(\d[\s\-\.]?){6,14}\d$/',
        message: 'Invalid phone number format'
    )]
    public ?string $phoneNumber = null;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 255)]
    public string $email;

    #[Gedmo\Versioned]
    #[ORM\Column]
    public bool $active;

    #[ORM\Column(type: 'text')]
    public string $address;

    #[ORM\Column(type: 'string', enumType: Gender::class)]
    public Gender $gender;

    public function canDrive(): bool
    {
        return $this->active
               && $this->licenseExpirationDate > new \DateTimeImmutable();
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getAge(): int
    {
        return $this->birthDate->diff(new \DateTimeImmutable())->y;
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
