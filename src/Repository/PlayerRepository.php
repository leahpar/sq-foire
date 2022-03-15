<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function findforRandom()
    {
        $date = new \DateTime();
        $date->modify("-1 hour");
        return $this->createQueryBuilder('p')
            ->andWhere('p.lastRandom is null')
            ->andWhere('p.lastConnection > :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $date
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkForReload($date)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.lastConnection > :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function findLikeDataTel($tel)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.data like :tel')
            ->setParameter(':tel', '%'.$tel.'%')
            //->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Player
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
