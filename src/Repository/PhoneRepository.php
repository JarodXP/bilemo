<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Phone;
use App\Service\Lister;
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
    use Lister;
    
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
    public function findList(int $page = null, int $limit = null):Paginator
    {
        return $this->findPaginatedList('p', 'name', $page, $limit);
    }
}
