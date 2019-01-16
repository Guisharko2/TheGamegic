<?php

namespace App\Repository;

use App\Entity\Oracle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Oracle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oracle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oracle[]    findAll()
 * @method Oracle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OracleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Oracle::class);
    }
}
