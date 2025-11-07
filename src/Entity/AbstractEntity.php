<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\MappedSuperclass]
#[Gedmo\Loggable]
abstract class AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    public ?Ulid $id;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column]
    public \DateTimeImmutable $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column]
    public \DateTimeImmutable $updatedAt;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $createdBy = null;

    #[Gedmo\Blameable(on: 'update')]
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $updatedBy = null;
}
