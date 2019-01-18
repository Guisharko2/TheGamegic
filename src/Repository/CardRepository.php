<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function findCardsByUser($user): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->where('u.id = :id')
            ->setParameters(['id' => $user->getId()])
            ->getQuery();

        return $qb->execute();
    }
}
