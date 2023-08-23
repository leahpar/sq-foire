<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function getNotes()
    {
        $query = $this->createQueryBuilder('a')
            ->leftJoin('a.question', 'q')
            ->leftJoin('q.hall', 'h')
            ->select("h.id, h.name, avg(a.answer) as score_avg, count(a.answer) as score_count")
            ->groupBy('q.hall')
            ->orderBy('score_avg', "desc")
        ;

        return $query->getQuery()->getResult();
    }

}
