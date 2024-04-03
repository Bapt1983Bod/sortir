<?php

namespace App\Repository;

use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findBySite(int $siteId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.site = :siteId')
            ->setParameter('siteId', $siteId)
            ->getQuery()
            ->getResult();
    }
    public function findByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.site', 'site')
            ->where('s.nom LIKE :keyword')
            ->orWhere('site.nom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->getQuery()
            ->getResult();
    }
    public function findByDateRange(DateTime $startDate, DateTime $endDate): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.site', 'site')
            ->where('s.dateHeureDebut BETWEEN :start_date AND :end_date')
            ->setParameter('start_date', $startDate)
            ->setParameter('end_date', $endDate)
            ->getQuery()
            ->getResult();
    }
}
