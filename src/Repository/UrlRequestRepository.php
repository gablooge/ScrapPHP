<?php

namespace App\Repository;

use App\Entity\UrlRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UrlRequest>
 *
 * @method UrlRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UrlRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UrlRequest[]    findAll()
 * @method UrlRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UrlRequest::class);
    }

    public function save(UrlRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UrlRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
