<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
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
    protected function findPaginatedList(string $tableAlias, string $orderField, int $page = null, int $limit = null, Company $company = null):Paginator
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

        if (!is_null($company)) {
            $queryBuilder->where($tableAlias.'.company='.$company->getId());
        }

        return new Paginator($queryBuilder->getQuery());
    }
}
