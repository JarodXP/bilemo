<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\User;
use App\Service\Lister;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    use Lister;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * findUserList
     * Gets a list of user paginated
     * @param mixed $page
     * @param mixed $limit
     * @return Paginator
     */
    public function findList(Company $company, int $page = null, int $limit = null):Paginator
    {
        return $this->findPaginatedList('u', 'lastName', $page, $limit, $company);
    }
}
