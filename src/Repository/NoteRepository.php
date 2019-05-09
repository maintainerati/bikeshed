<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Maintainerati\Bikeshed\Entity\Note;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * @return Note[] Returns an array of Note objects
     */
    public function findIdsBy(iterable $values): ?array
    {
        $query = $this->createQueryBuilder('n')
            ->orderBy('n.date', 'ASC')
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
}
