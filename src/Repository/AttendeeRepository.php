<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Maintainerati\Bikeshed\Entity\Attendee;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Attendee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attendee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attendee[]    findAll()
 * @method Attendee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendeeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Attendee::class);
    }
}
