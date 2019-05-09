<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Maintainerati\Bikeshed\Entity\Session;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * @return Session[] Returns an array of Session objects
     */
    public function findIdsBy(iterable $values): ?array
    {
        $query = $this->createQueryBuilder('s')
            ->orderBy('s.date', 'ASC')
        ;
        foreach ($values as $parameter => $value) {
            $query
                ->andWhere("s.$parameter = :$parameter")
                ->setParameter($parameter, $value)
            ;
        }

        return $query
            ->getQuery()
            ->getResult()
        ;
    }
}
