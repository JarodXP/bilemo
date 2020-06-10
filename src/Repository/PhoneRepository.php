<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Phone;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Phone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phone[]    findAll()
 * @method Phone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phone::class);
    }
    
    /**
     * findPaginatedList
     * Gets a list of phones paginated
     * @param mixed $page
     * @param mixed $limit
     * @return Paginator
     */
    public function findPaginatedList(int $page = null, int $limit = null):Paginator
    {
        //Sets default params value
        if ($page == null) {
            $page = 1;
        }

        if ($limit == null) {
            $limit = 5;
        }

        //Builds query
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->select('p')
            ->setFirstResult(($page-1)*$limit)
            ->setMaxResults($limit)
            ->orderBy('p.name');

        return new Paginator($queryBuilder->getQuery());
    }
}
