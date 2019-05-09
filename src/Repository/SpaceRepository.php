<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Maintainerati\Bikeshed\Entity\Space;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Space|null find($id, $lockMode = null, $lockVersion = null)
 * @method Space|null findOneBy(array $criteria, array $orderBy = null)
 * @method Space[]    findAll()
 * @method Space[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpaceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Space::class);
    }

    /**
     * @return Space[] Returns an array of Space objects
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
