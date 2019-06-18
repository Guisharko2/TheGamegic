<?php

namespace App\Repository;

use App\Entity\DeckCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DeckCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeckCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeckCard[]    findAll()
 * @method DeckCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeckCardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeckCard::class);
    }

    public function findDeckByCard($idCard, $idDeck): array
    {
        $qb = $this->createQueryBuilder('f')
            ->join('f.deck', 'd')
            ->where('d.id = :did')
            ->andWhere('f.idCard = :card')
            ->setParameters(['card' => $idCard,'did' => $idDeck ])
            ->getQuery();

        return $qb->execute();
    }
}
