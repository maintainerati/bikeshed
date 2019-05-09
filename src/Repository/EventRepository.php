<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Maintainerati\Bikeshed\Entity\Event;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findIdsBy(iterable $values): ?array
    {
        $query = $this->createNativeNamedQuery('e')
            ->orderBy('e.date', 'ASC')
        ;
        foreach ($values as $parameter => $value) {
            $query
                ->andWhere("n.$parameter = :$parameter")
                ->setParameter($parameter, $value)
            ;
        }

        return $query
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCurrent(): ?Event
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'ASC')
            ->andWhere('e.date >= :date')
            ->setParameter('date', new \DateTimeImmutable())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
