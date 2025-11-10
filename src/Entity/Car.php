<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\ApiPlatform\Filter\GlobalSearchFilter;
use App\Entity\Trait\AgencyTrait;
use App\Enum\CarCategory;
use App\Enum\CarStatus;
use App\Enum\FuelType;
use App\Enum\Transmission;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\CarRepository::class)]
#[ORM\Table(name: 'cars')]
#[ApiResource(
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 15,
    paginationMaximumItemsPerPage: 30
)]
#[ApiFilter(SearchFilter::class, properties: [
    'registrationNumber' => 'ipartial',
    'brand' => 'ipartial',
    'model' => 'ipartial',
    'color' => 'ipartial',
    'fuelType' => 'exact',
    'transmission' => 'exact',
    'category' => 'exact',
    'status' => 'exact',
])]
#[ApiFilter(BooleanFilter::class, properties: ['active', 'isUnderMaintenance'])]
#[ApiFilter(OrderFilter::class, properties: [
    'brand', 'model', 'year', 'mileage', 'dailyRentalPrice', 'createdAt',
    'status', 'registrationNumber'
])]
#[ApiFilter(DateFilter::class, properties: [
    'acquisitionDate', 'insuranceExpirationDate',
    'lastServiceAt', 'nextServiceAt'
])]
#[ApiFilter(GlobalSearchFilter::class, properties: [
    'fields' => ['registrationNumber','brand','model','color']
])]
class Car extends AbstractEntity
{
    use AgencyTrait;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    public string $registrationNumber;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    public string $brand;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    public string $model;

    #[Gedmo\Versioned]
    #[ORM\Column]
    #[Assert\Range(min: 1950, max: 2100)]
    public int $year;

    #[Gedmo\Versioned]
    #[ORM\Column(type: 'string', enumType: CarCategory::class)]
    public CarCategory $category;

    #[Gedmo\Versioned]
    #[ORM\Column(type: 'string', enumType: FuelType::class)]
    public FuelType $fuelType;

    #[Gedmo\Versioned]
    #[ORM\Column(type: 'string', enumType: Transmission::class)]
    public Transmission $transmission;

    #[Gedmo\Versioned]
    #[ORM\Column(length: 30, nullable: true)]
    public ?string $color = null;

    #[Gedmo\Versioned]
    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\PositiveOrZero]
    public int $mileage = 0;

    #[Gedmo\Versioned]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['unsigned' => true])]
    #[Assert\PositiveOrZero]
    public string $dailyRentalPrice = '0.00';

    #[Gedmo\Versioned]
    #[ORM\Column(type: 'string', enumType: CarStatus::class)]
    public CarStatus $status = CarStatus::Available;

    #[Gedmo\Versioned]
    #[ORM\Column]
    public bool $active = true;

    #[Gedmo\Versioned]
    #[ORM\Column]
    public bool $isUnderMaintenance = false;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $lastServiceAt = null;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $nextServiceAt = null;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $insuranceExpirationDate = null;

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $acquisitionDate = null;

    #[Gedmo\Versioned]
    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\Positive]
    public int $seats = 5;

    #[Gedmo\Versioned]
    #[ORM\Column(options: ['unsigned' => true])]
    #[Assert\Positive]
    public int $doors = 5;

    public function isAvailableForRental(?\DateTimeImmutable $on = null): bool
    {
        $on ??= new \DateTimeImmutable();

        $insuranceOk = !$this->insuranceExpirationDate || $this->insuranceExpirationDate > $on;

        return $this->active
            && !$this->isUnderMaintenance
            && $this->status === CarStatus::Available
            && $insuranceOk;
    }

    public function getDisplayName(): string
    {
        return sprintf('%s %s (%s)', $this->brand, $this->model, $this->registrationNumber);
    }
}
