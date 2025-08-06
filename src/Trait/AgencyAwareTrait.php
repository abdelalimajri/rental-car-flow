<?php
namespace App\Trait;

use App\Entity\Agency;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait AgencyAwareTrait
{
    #[ORM\ManyToOne(targetEntity: Agency::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agency $agency = null;

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): static
    {
        $this->agency = $agency;
        return $this;
    }

    /**
     * Check if entity belongs to specific agency
     */
    public function belongsToAgency(Agency $agency): bool
    {
        return $this->agency && $this->agency->getId() === $agency->getId();
    }

    /**
     * Get agency ID for comparisons
     */
    public function getAgencyId(): ?int
    {
        return $this->agency?->getId();
    }
}