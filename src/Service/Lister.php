<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;

trait Lister
{
    /**
     * findPaginatedList
     * Gets a list of entities paginated
     * @param mixed $page
     * @param mixed $limit
     * @param string $tableAlias
     * @param string $orderField
     * @return Paginator
     */
    protected function findPaginatedList(int $page = null, int $limit = null, string $tableAlias, string $orderField):Paginator
    {
        //Sets default params value
        if ($page == null) {
            $page = 1;
        }

        if ($limit == null) {
            $limit = 5;
        }

        //Builds query
        $queryBuilder = $this->createQueryBuilder($tableAlias);

        $queryBuilder
            ->select($tableAlias)
            ->setFirstResult(($page-1)*$limit)
            ->setMaxResults($limit)
            ->orderBy($tableAlias.'.'.$orderField);

        return new Paginator($queryBuilder->getQuery());
    }
}
