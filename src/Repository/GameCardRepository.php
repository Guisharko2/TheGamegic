<?php

namespace App\Repository;

use App\Entity\GameCard;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GameCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameCard[]    findAll()
 * @method GameCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameCardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GameCard::class);
    }
    public function basicSearch(string $search)
    {
        $query = $this->createQueryBuilder('b')
            ->where('b.name LIKE :search')
            ->orWhere('b.printed_name LIKE :search')
            ->andWhere('b.lang LIKE :fr')
            ->setParameter('search', "%$search%")
            ->setParameter('fr', "%fr%")
            ->setMaxResults(60)
            ->getQuery();
        return $query->getResult();
    }


    public function findCardsByUser($user): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->setParameters(['id' => $user ])
            ->where('u.id = :id')
            ->getQuery();

        return $qb->execute();
    }
    // /**
    //  * @return GameCard[] Returns an array of GameCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
