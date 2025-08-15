<?php

namespace App\Entity\Trait;

use App\Entity\Agency;
use Doctrine\ORM\Mapping as ORM;

trait AgencyTrait
{
    #[ORM\ManyToOne(targetEntity: Agency::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)] // Nullable for SUPER_ADMIN role
    public ?Agency $agency = null;

    public function hasAgency(): bool
    {
        return null !== $this->agency;
    }

    public function getAgencyName(): ?string
    {
        return $this->agency?->name;
    }

    public function getAgencyId(): ?string
    {
        return $this->agency?->id?->toBase32();
    }

    public function isSameAgency(?Agency $agency): bool
    {
        if (!$this->agency || !$agency) {
            return false;
        }

        return $this->agency->id->equals($agency->id);
    }
}
