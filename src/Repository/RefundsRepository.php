<?php

namespace App\Repository;

use App\Entity\Refunds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Refunds>
 *
 * @method Refunds|null find($id, $lockMode = null, $lockVersion = null)
 * @method Refunds|null findOneBy(array $criteria, array $orderBy = null)
 * @method Refunds[]    findAll()
 * @method Refunds[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefundsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Refunds::class);
    }

//    /**
//     * @return Refunds[] Returns an array of Refunds objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Refunds
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
