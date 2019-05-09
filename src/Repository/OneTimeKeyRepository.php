<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Repository;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Maintainerati\Bikeshed\Entity\OneTimeKey;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OneTimeKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method OneTimeKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method OneTimeKey[]    findAll()
 * @method OneTimeKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OneTimeKeyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OneTimeKey::class);
    }

    public function findValid(): ?array
    {
        return $this->createQueryBuilder('o')
            ->where('o.validUntil >= :date')
            ->setParameter('date', (new DateTimeImmutable())->setTime(0, 0, 0))
            ->orderBy('o.validUntil', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findExpired(): ?array
    {
        return $this->createQueryBuilder('o')
            ->where('o.validUntil < :date')
            ->setParameter('date', (new DateTimeImmutable())->setTime(0, 0, 0))
            ->orderBy('o.validUntil', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUntilDate(DateTimeInterface $date): ?array
    {
        return $this->createQueryBuilder('o')
            ->where('o.validUntil <= :date')
            ->setParameter('date', $date)
            ->orderBy('o.validUntil', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
