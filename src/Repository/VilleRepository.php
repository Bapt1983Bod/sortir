<?php

namespace App\Repository;

use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ville>
 *
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function findByKeyword(string $keyword) : array
    {
        return $this->createQueryBuilder('ville')
            ->where('ville.nom LIKE :keyword')
            ->setParameter('keyword','%'.$keyword.'%')
            ->getQuery()
            ->getResult();
    }

    public function findByNameCp($nom, $cp) : array
    {
        return $this->createQueryBuilder('v')
            ->where('v.nom = :nom')
            -> setParameter( 'nom', $nom )
            -> andWhere( 'v.codePostal = :cp')
            ->setParameter('cp', $cp)
            ->getQuery()
            ->getResult();
    }
}